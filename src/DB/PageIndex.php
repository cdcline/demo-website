<?php declare(strict_types=1);

namespace DB;

use DB\DBTrait;
use DB\PageHeaderImages;
use Pages\BasePage;
use Pages\InvalidPageException;
//use Utils\FirestoreUtils;

class PageIndex {
   use DBTrait;

   const DEFAULT_TYPE = 'default';
   const HOMEPAGE_TYPE = 'homepage';
   const WORK_TYPE = 'work';
   const ROBOTS_TYPE = 'robots';

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
      /*
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
      */
      return [];
   }

   private static function getDevStaticData(): array {
      return array_map(
         fn($iPageValues) => self::fromArray($iPageValues),
         self::getDevStaticRows()
      );
   }

   private static function getLiveStaticData(): array {
      return array_map(
         fn($iPageValues) => self::fromArray($iPageValues),
         self::getLiveStaticRows()
      );
   }

   private static function getDevStaticRows(): array {
      return [
         ['type' => self::HOMEPAGE_TYPE,
          'theme' => self::ORANGE_THEME,
          'pageid' => 1,
          'page_title' => 'Welcome - My Demo Website',
          'page_header' => 'Welcome',
          'nav_text' => 'You might also enjoy...',
          'main_article' => <<<EOT
## Welcome to My Website!

I'm a "Web Developer" who enjoys creating order from chaos. I've been [writing code](https://github.com/cdcline) since [elementry school](https://en.wikipedia.org/wiki/Logo_(programming_language)) and this is [my demo website](https://github.com/cdcline/demo-website).

I wanted a website that would be fun & interactive. I wanted to write most of the code so I could play with how all the pieces of website worked. I knew it would be a [large undertaking](https://github.com/cdcline/demo-website/issues/12) but I had the Summer off and lots of free time.

### The Beginning

I did some research and though I could get a "basic website" working in [about one week](https://github.com/cdcline/demo-website/issues/1). While mostly successful in estimation, like any coding project, the feature list quickly expanded.

I had originally intended "something simple":
  * Loading the backend through Google Cloud
  * Support loading a unique page off of an arbitrary url
  * Basic CSS and JS
  * Displaying some _parsable text_
    * **[\[click here to see unparsed text\]](#toggleParser)**
  * Supporting arbitrary HTML stuff on each page
    * Could be "unique" html to the page
    * Could be a "widget" that could be added to any page

### The Evolution
By the time I had my "simple" website ready, it had been about 2 weeks of development and I had a [functional but empty website](https://github.com/cdcline/demo-website/issues/12#issuecomment-1116862020). I felt like I had built a house but all the rooms were empty. So I spent a week adding [some content and style](https://github.com/cdcline/demo-website/issues/12#issuecomment-1120445080).

I found myself with a website that looked ok. It had all the basics of a website but I wanted more. Something unique with animation and graphics. I knew much better designers so I hired one to [help figure out a site map](https://github.com/cdcline/demo-website/issues/22#issuecomment-1133763883) and an actual [design for each page](https://github.com/cdcline/demo-website/issues/65).

### The Result
The final design required [a lot of changes](https://github.com/cdcline/demo-website/issues/83) but in [the end](https://github.com/cdcline/demo-website/issues/105) I've got a cool sandbox to play in!   I ended up having a lot of fun with animations on this page. Try clicking [my profile picture](#welcome-header-container).
EOT
         ],
         ['type' => self::WORK_TYPE,
          'theme' => self::GREY_THEME,
          'pageid' => 2,
          'page_title' => 'Work - My Website Demo',
          'page_header' => 'Work',
         ],
         ['type' => self::ROBOTS_TYPE,
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
          'main_article' => ''
         ],
      ];
   }

   private static function getLiveStaticRows(): array {
      $devRows = self::getDevStaticRows();
      return [
         $devRows[0],
         ['type' => self::WORK_TYPE,
          'theme' => self::GREY_THEME,
          'pageid' => 2,
          'page_title' => 'Work - Website Demo',
          'page_header' => 'Work',
         ],
         ['type' => self::ROBOTS_TYPE,
          'theme' => self::BLACK_THEME,
          'pageid' => 3,
          'page_title' => 'Robots - Website Demo',
          'page_header' => 'Robots',
          'main_article' => <<<EOT
## Robots

### Franky ![Franky](https://storage.googleapis.com/burnished-flare-348022.appspot.com/images/site/c2d2.png)

Franky was our first attempt at an autonomous digging robot. He got his name as more parts were grafted onto the body as the "features" evolved.
EOT
         ],
         ['type' => self::DEFAULT_TYPE,
          'theme' => self::GREEN_THEME,
          'pageid' => 4,
          'page_title' => 'Life - Website Demo',
          'page_header' => 'Life',
          'hide_main_nav' => true,
          'main_article' => ''
         ],
      ];
   }
}
