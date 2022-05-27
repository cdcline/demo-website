<?php declare(strict_types=1);

namespace DB;

use DB\DBTrait;
use Pages\InvalidPageException;
use Utils\StringUtils;
use Pages\BasePage;
use Utils\FirestoreUtils;
use Utils\SiteRunner;

class PageNav {
   use DBTrait;

   public const ARTICLE_TYPE = 'ARTICLE';
   public const FOOTER_TYPE = 'FOOTER';
   public const DEFAULT_PAGEID = 1;

   private $slug;
   private $nav_string;
   private $orderby;
   // Odd to need a pageid here but we use it to generate the link URLs
   private $pageid;
   private $type;

   public static function getPageFromSlug(string $slug): BasePage {
      // If it's an int looking string, assume we want to load by pageid else lookup a pageid from page_nav.slug
      $pageNav = StringUtils::isInt($slug) ? PageIndex::getPageFromPageid((int)$slug) : self::getPageNavFromSlug($slug);
      return PageIndex::getPageFromPageid($pageNav->getPageid());
   }

   public static function getDefaultNav(): self {
      return self::getPageNavFromPageid(self::DEFAULT_PAGEID);
   }

   public static function getDefaultSlug(): string {
      return self::getDefaultNav()->getSlug();
   }

   public function toArray(): array {
      return [
         'type' => $this->type,
         'theme' => $this->getTheme(),
         'slug' => $this->slug,
         'url' => $this->getUrl(),
         'nav_string' => $this->navString,
         'pageid' => $this->pageid,
         'orderby' => $this->orderby
      ];
   }

   public function displayInNav(): bool {
      return $this->isArticleLink() && !$this->isDisplayedPage();
   }

   private static function getPageNavFromSlug(string $slug): self {
      foreach (self::fetchAllRowsFromStaticCache() as $pNav) {
         if ($pNav->matchesSlug($slug)) {
            return $pNav;
         }
      }
      InvalidPageException::throwPageNotFound($slug);
   }

   private static function fromArray(array $aData) {
      return new self(
         $aData['slug'],
         $aData['nav_string'],
         $aData['type'],
         (int)$aData['orderby'],
         (int)$aData['pageid']
      );
   }

   private static function fetchAllRows(): array {
      $path = 'page_nav';
      $iDocs = ['slug', 'nav_string', 'orderby'];
      $iSnaps = [
         FirestoreUtils::buildSnap('page', 'pageid', 'pageid'),
         FirestoreUtils::buildSnap('type', 'enum'),
      ];
      return array_map(
         fn($aValues) => self::fromArray($aValues),
         self::fetchRows($path, $iDocs, $iSnaps)
      );
   }

   private static function getPageNavFromPageid(int $pageid): self {
      foreach (self::fetchAllRowsFromStaticCache() as $pNav) {
         if ($pNav->matchesPageid($pageid)) {
            return $pNav;
         }
      }
      InvalidPageException::throwPageNotFound($pageid);
   }

   private function __construct(string $slug, string $navString, string $type, int $orderby, int $pageid) {
      $this->slug = $slug;
      $this->navString = $navString;
      $this->type = $type;
      $this->orderby = $orderby;
      $this->pageid = $pageid;
   }

   private function isArticleLink(): bool {
      return StringUtils::iMatch($this->type, self::ARTICLE_TYPE);
   }

   private function isDisplayedPage(): bool {
      $slug = SiteRunner::getSlugFromUrl();
      return StringUtils::iMatch($slug, $this->slug);
   }

   private function getUrl(): string {
      // We'll assume the slug is a full URL in the footer
      if ($this->isFooterLink()) {
         return $this->slug;
      }

      // Don't have any url for the "homepage" link.
      if ($this->isDefaultArticle()) {
         return $this->getHomepageLink();
      }

      return $this->getArticleLink();
   }

   private function getHomepageLink(): string {
      return '/';
   }

   // We'll assume all article links are relative
   private function getArticleLink(): string {
      // Pages can be loaded on a slug or pageid.
      // Slugs look pretty so we'll prefer those.
      $linkText = $this->slug ?: $this->pageid;
      return "/{$linkText}";
   }

   private function isDefaultArticle(): bool {
      return $this->pageid === PageNav::DEFAULT_PAGEID;
   }

   private function isFooterLink(): bool {
      return $this->type === self::FOOTER_TYPE;
   }

   private function getSlug(): string {
      return $this->slug;
   }

   public function getPageid(): int {
      return $this->pageid;
   }

   private function matchesPageid(int $pageid): bool {
      return $this->pageid === $pageid;
   }

   private function matchesSlug(string $slug): bool {
      return StringUtils::iMatch($this->slug, $slug);
   }

   private function getTheme(): string {
      return $this->getPageid() ? PageIndex::getThemeFromPageid($this->getPageid()) : PageIndex::PURPLE_THEME;
   }

   // NOTE: Order of the data matters, should match `fetchAllRows`
   private static function getHardcodedRows(): array {
      $values = [
         ['navid' => 1,
          'type' => self::ARTICLE_TYPE,
          'slug' => 'homepage',
          'nav_string' => 'Homepage',
          'pageid' => 1,
          'orderby' => 1
         ],
         ['navid' => 2,
          'type' => self::FOOTER_TYPE,
          'slug' => 'https://github.com/cdcline/demo-website',
          'nav_string' => 'Resume',
          'pageid' => NULL,
          'orderby' => 2
         ],
         ['navid' => 3,
          'type' => self::ARTICLE_TYPE,
          'slug' => 'dev',
          'nav_string' => 'Dev',
          'pageid' => 2,
          'orderby' => 2
         ],
         ['navid' => 4,
          'type' => self::FOOTER_TYPE,
          'slug' => 'https://www.linkedin.com/in/cdcline/',
          'nav_string' => 'LinkedIn',
          'pageid' => NULL,
          'orderby' => 1
         ],
         ['navid' => 5,
          'type' => self::FOOTER_TYPE,
          'slug' => 'mailto:1248182+cdcline@users.noreply.github.com',
          'nav_string' => 'Contact Me',
          'pageid' => NULL,
          'orderby' => 3
         ],
         ['navid' => 6,
          'type' => self::ARTICLE_TYPE,
          'slug' => 'test-page-1',
          'nav_string' => 'Test Page 1',
          'pageid' => 3,
          'orderby' => 3
         ],
         ['navid' => 7,
          'type' => self::ARTICLE_TYPE,
          'slug' => 'test-page-2',
          'nav_string' => 'Test Page 2',
          'pageid' => 4,
          'orderby' => 4
         ]
      ];
      return array_map(fn($vals) => self::fromArray($vals), $values);
   }
}
