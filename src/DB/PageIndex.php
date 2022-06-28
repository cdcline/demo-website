<?php declare(strict_types=1);

namespace DB;

use DB\DBTrait;
use DB\PageHeaderImages;
use Pages\BasePage;
use Pages\InvalidPageException;
use Utils\FirestoreUtils;

class PageIndex {
   use DBTrait;

   const DEFAULT_TYPE = 'default';
   const HOMEPAGE_TYPE = 'homepage';
   const WORK_TYPE = 'work';

   const ORANGE_THEME = 'orange';
   const GREY_THEME = 'grey';
   const GREEN_THEME = 'green';
   const BLACK_THEME = 'black';

   private $pageid;
   private $pageTitle;
   private $pageHeader;
   private $pageHeaderImages;
   private $pageType;
   private $mainArticle;
   private $navText;
   private $hideMainNav;
   private $theme;

   public static function getThemeFromPageid(int $pageid): string {
      return self::getPageIndexFromPageid($pageid)->getTheme();
   }

   public static function getPageFromPageid(int $pageid): BasePage {
      return self::getPageIndexFromPageid($pageid)->getPage();
   }

   public function getPageTitle(): string {
      return $this->pageTitle;
   }

   public function getPageHeader(): string {
      return $this->pageHeader;
   }

   public function getFullHeaderImages(): array {
      return $this->pageHeaderImages->toFullArray();
   }

   public function getMobileHeaderImages(): array {
      return $this->pageHeaderImages->toMobileArray();
   }

   public function getPageType(): string {
      return $this->pageType;
   }

   public function getMainArticle(): string {
      return $this->mainArticle;
   }

   public function getTheme(): string {
      return $this->theme;
   }

   public function getPageid(): int {
      return $this->pageid;
   }

   public function getNavText(): string {
      return (string)$this->navText;
   }

   public function getHideMainNav(): bool {
      return $this->hideMainNav;
   }

   public function getPage(): BasePage {
      $pageClass = BasePage::getClassNameFromPageType($this->getPageType());
      return new $pageClass($this);
   }

   private static function getPageIndexFromPageid(int $pageid): self {
      foreach (self::fetchAllRowsFromStaticCache() as $iPage) {
         if ($iPage->matchesPageid($pageid)) {
            return $iPage;
         }
      }
      InvalidPageException::throwPageNotFound($pageid);
   }

   public function toArray(): array {
      return [
         'type' => $this->getPageType(),
         'pageid' => $this->getPageid(),
         'page_title' => $this->getPageTitle(),
         'page_header' => $this->getPageHeader(),
         'full_header_images' => $this->getFullHeaderImages(),
         'mobile_header_images' => $this->getMobileHeaderImages(),
         'main_article' => $this->getMainArticle(),
         'nav_text' => $this->getNavText(),
         'hide_main_nav' => $this->getHideMainNav(),
         'theme' => $this->getTheme()
      ];
   }

   private static function fromArray(array $iPageValues) {
      $pageHeaderImages = PageHeaderImages::fromPageid($iPageValues['pageid']);
      return new self(
         (int)$iPageValues['pageid'],
         $iPageValues['page_title'] ?? 'Unknown Title',
         $iPageValues['page_header'] ?? 'Unknown Header',
         $iPageValues['type'] ?? self::DEFAULT_TYPE,
         $iPageValues['main_article'] ?? '',
         $iPageValues['nav_text'] ?? '',
         isset($iPageValues['hide_main_nav']) ? (bool)$iPageValues['hide_main_nav'] : false,
         $iPageValues['theme'] ?? self::GREEN_THEME,
      );
   }

   private function __construct(int $pageid, string $pageTitle, string $pageHeader, string $pageType, string $mainArticle, string $navText, bool $hideMainNav, string $theme) {
      $this->pageid = $pageid;
      $this->pageTitle = $pageTitle;
      $this->pageHeader = $pageHeader;
      $this->pageType = $pageType;
      $this->mainArticle = $mainArticle;
      $this->navText = $navText;
      $this->hideMainNav = $hideMainNav;
      $this->theme = $theme;
      $this->pageHeaderImages = PageHeaderImages::fromPageid($pageid);
   }

   private function matchesPageid(int $pageid): bool {
      return $this->getPageid() === $pageid;
   }

   private static function fetchAllRows(): array {
      $path = FirestoreUtils::indexPagesPath();
      $iDocs = ['pageid', 'main_article', 'page_header', 'page_title', 'nav_text', 'hide_main_nav', 'theme'];
      $iSnaps = [FirestoreUtils::buildSnap('type', 'enum')];
      $fromFirestoreFnc = function($iPageValues): array {
         $iPageValues['main_article'] = FirestoreUtils::hackNewlines($iPageValues['main_article']);
         return $iPageValues;
      };
      return array_map(
         fn($iPageValues) => self::fromArray($iPageValues),
         self::fetchRows($path, $iDocs, $iSnaps, $fromFirestoreFnc)
      );
   }

   private static function getHardcodedRows(): array {
      return array_map(
         fn($iPageValues) => self::fromArray($iPageValues),
         self::getStaticRows()
      );
   }

   private static function getStaticRows(): array {
      return [
         ['type' => self::HOMEPAGE_TYPE,
          'theme' => self::ORANGE_THEME,
          'pageid' => 1,
          'page_title' => 'Welcome - My Demo Website',
          'page_header' => 'Welcome',
          'nav_text' => 'You might also enjoy...',
          'main_article' => <<<EOT
## Welcome to My Personal Website!

I've been [writing code](#link) since [elementry school](https://en.wikipedia.org/wiki/Logo_(programming_language))!

I write code and didn't have _any_ coding examples or even a server to run my site on! I hope to solve this with My Personal Website. It will be both as my personal coding playground and an example of how I write code!

#### This page is an example of "My Demo Website" capabilities!

All the text you've read so far is **[parsable](#toggleParser)** _text_ and should be easy for anyone to ~~etid~~ edit.

This is great for:
* Speed
 * You can just write text and not worry about html
* Frequent updating
 * Easy to find the text to change
* Non-Coders
 * Don't have to know anything about CSS or HTML
* Readability
 * It's a block of text with kinda random punctuation.
 * No `<code>` elements to worry about.

However, it has it's limits. You can't really do fancy **frontend** _things_.
EOT
         ],
         ['type' => self::WORK_TYPE,
          'theme' => self::GREY_THEME,
          'pageid' => 2,
          'page_title' => 'Work - My Website Demo',
          'page_header' => 'Work',
         ],
         ['type' => self::DEFAULT_TYPE,
          'theme' => self::BLACK_THEME,
          'pageid' => 3,
          'page_title' => 'Test 3 - Website Demo',
          'page_header' => 'Test Page 3',
          'main_article' => <<<EOT
## Robots

### Franky ![Franky](src/images/site/fun-robot.png)

Franky was our first attempt at an autonomous digging robot. He got his name as more parts were grafted onto the body as the "features" evolved.
EOT
         ],
         ['type' => self::DEFAULT_TYPE,
          'theme' => self::GREEN_THEME,
          'pageid' => 4,
          'page_title' => 'Life - Website Demo',
          'page_header' => 'Life',
          'hide_main_nav' => true,
          'main_article' => <<<EOT
## Life

Vulputate dignissim suspendisse in est. Amet risus nullam eget felis eget nunc lobortis. Pellentesque diam volutpat commodo sed egestas. Id leo in vitae turpis massa sed elementum tempus egestas. Nam libero justo laoreet sit amet cursus sit. Consectetur purus ut faucibus pulvinar. Laoreet suspendisse interdum consectetur libero id faucibus nisl. Laoreet non curabitur gravida arcu ac tortor dignissim convallis aenean. Viverra mauris in aliquam sem fringilla. Nibh nisl condimentum id venenatis a condimentum vitae sapien pellentesque. Ut placerat orci nulla pellentesque. Bibendum at varius vel pharetra vel turpis nunc eget lorem. Euismod quis viverra nibh cras pulvinar mattis nunc sed.
EOT
         ],
      ];
   }
}
