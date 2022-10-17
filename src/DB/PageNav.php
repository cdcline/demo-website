<?php declare(strict_types=1);

namespace DB;

use DB\DBTrait;
use Pages\InvalidPageException;
use Utils\StringUtils;
use Pages\BasePage;
//use Utils\FirestoreUtils;
use Utils\SiteRunner;
use Utils\SiteUrl;

class PageNav {
   use DBTrait;

   public const DEFAULT_PAGEID = 1;

   private const MAIN_TYPE = 'MAIN';
   private const FOOTER_TYPE = 'FOOTER';

   private $section;
   private $slug;
   private $nav_string;
   private $orderby;
   // Odd to need a pageid here but we use it to generate the link URLs
   private $pageid;
   private $type;
   private $imgSrc;

   public static function getPageFromSlug(string $slug): BasePage {
      // If it's an int looking string, assume we want to load by pageid else lookup a pageid from page_nav.slug
      $pageNav = StringUtils::isInt($slug) ? PageIndex::getPageFromPageid((int)$slug) : self::getPageNavFromSlug($slug);
      return PageIndex::getPageFromPageid($pageNav->getPageid());
   }

   public static function getRedirectFromSlug(string $slug): ?string {
      foreach (self::getRedirects() as $rInfo) {
         foreach ($rInfo['slugs'] as $redirectSlug) {
            if (StringUtils::iMatch($slug, $redirectSlug)) {
               return $rInfo['url'];
            }
         }
      }
      return null;
   }

   public static function getDefaultNav(): self {
      return self::getPageNavFromPageid(self::DEFAULT_PAGEID);
   }

   public static function getDefaultSlug(): string {
      return self::getDefaultNav()->getSlug();
   }

   public function toArray(): array {
      return [
         'is_viewed' => $this->isDisplayedPage(),
         'is_image' => $this->isImageLink(),
         'img_src' => $this->imgSrc,
         'type' => $this->type,
         'section' => $this->section,
         'slug' => $this->slug,
         'url' => $this->getUrl(),
         'nav_string' => $this->navString,
         'pageid' => $this->pageid,
         'orderby' => $this->orderby
      ];
   }

   public function displayInNav(): bool {
      return $this->isArticleLink();
   }

   public function displayInFooter(): bool {
      return $this->isFooterLink();
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
         $aData['section'] ?? null,
         (int)$aData['orderby'],
         (int)$aData['pageid'],
         $aData['img_src'] ?? null
      );
   }

   private static function fetchAllRows(): array {
      /*
      $path = 'page_nav';
      $iDocs = ['section', 'img_src', 'slug', 'nav_string', 'orderby'];
      $iSnaps = [
         FirestoreUtils::buildSnap('page', 'pageid', 'pageid'),
         FirestoreUtils::buildSnap('type', 'enum'),
      ];
      return array_map(
         fn($aValues) => self::fromArray($aValues),
         self::fetchRows($path, $iDocs, $iSnaps)
      );
      */
      return [];
   }

   private static function getPageNavFromPageid(int $pageid): self {
      foreach (self::fetchAllRowsFromStaticCache() as $pNav) {
         if ($pNav->matchesPageid($pageid)) {
            return $pNav;
         }
      }
      InvalidPageException::throwPageNotFound($pageid);
   }

   private function __construct(string $slug, string $navString, string $type, ?string $section, int $orderby, int $pageid, ?string $imgSrc) {
      $this->section = $section;
      $this->slug = $slug;
      $this->navString = $navString;
      $this->type = $type;
      $this->orderby = $orderby;
      $this->pageid = $pageid;
      $this->imgSrc = $imgSrc;
   }

   private function isArticleLink(): bool {
      return StringUtils::iMatch($this->type, self::MAIN_TYPE);
   }

   private function isImagelink(): bool {
      return (bool)$this->imgSrc;
   }

   private function isDisplayedPage(): bool {
      $slug = SiteRunner::getSlugFromUrl();
      return StringUtils::iMatch($slug, $this->slug);
   }

   private function getUrl(): string {
      // We'll assume the slug is a full URL in the footer
      if ($this->isFooterLink() || !$this->pageid) {
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
      return StringUtils::iMatch($this->type, self::FOOTER_TYPE);
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

   private static function getDevStaticData(): array {
      return self::getHardcodedRows();
   }

   private static function getLiveStaticData(): array {
      return self::getHardcodedRows();
   }

   private static function getRedirects(): array {
      return [
         ['slugs' => ['resume', 'résumé'],
          'url' => SiteUrl::getResume(/*hostedFile*/true)
         ],
      ];
   }

   // NOTE: Order of the data matters, should match `fetchAllRows`
   private static function getHardcodedRows(): array {
      $values = [
         ['navid' => 1,
          'type' => self::MAIN_TYPE,
          'section' => 'About',
          'slug' => 'welcome',
          'nav_string' => 'Welcome',
          'pageid' => 1,
          'orderby' => 1
         ],
         ['navid' => 2,
          'type' => self::MAIN_TYPE,
          'section' => 'About',
          'slug' => 'robots',
          'nav_string' => 'Robots',
          'pageid' => 3,
          'orderby' => 2
         ],
         ['navid' => 3,
          'type' => self::MAIN_TYPE,
          'section' => 'About',
          'slug' => 'work',
          'nav_string' => 'Work',
          'pageid' => 2,
          'orderby' => 3
         ],
         ['navid' => 4,
          'type' => self::MAIN_TYPE,
          'section' => 'About',
          'slug' => 'life',
          'nav_string' => 'Life',
          'pageid' => 4,
          'orderby' => 4
         ],
         ['navid' => 5,
          'type' => self::MAIN_TYPE,
          'section' => 'Contact',
          'slug' => 'https://www.linkedin.com/in/cdcline/',
          'img_src' => 'src/images/site/linkedin_logo_64px.png',
          'nav_string' => 'LinkedIn',
          'pageid' => NULL,
          'orderby' => 1
         ],
         ['navid' => 6,
          'type' => self::MAIN_TYPE,
          'section' => 'Contact',
          'slug' => 'https://github.com/cdcline/demo-website',
          'img_src' => 'src/images/site/github_logo_64px.png',
          'nav_string' => 'Resume',
          'pageid' => NULL,
          'orderby' => 2
         ],
         ['navid' => 7,
          'type' => self::MAIN_TYPE,
          'section' => 'Code Features',
          'slug' => 'versatility',
          'nav_string' => 'Versatility',
          'pageid' => 2,
          'orderby' => 1
         ],
         ['navid' => 8,
          'type' => self::MAIN_TYPE,
          'section' => 'Code Features',
          'slug' => 'accessibility',
          'nav_string' => 'Accessibility',
          'pageid' => 3,
          'orderby' => 2
         ],
         ['navid' => 9,
          'type' => self::MAIN_TYPE,
          'section' => 'Code Features',
          'slug' => 'scalability',
          'nav_string' => 'Scalability',
          'pageid' => 4,
          'orderby' => 3
         ],
         ['navid' => 5,
          'type' => self::FOOTER_TYPE,
          'slug' => 'welcome',
          'img_src' => 'src/images/site/nav/welcome.png',
          'nav_string' => 'Home',
          'pageid' => 1,
          'orderby' => 1
         ],
         ['navid' => 7,
          'type' => self::FOOTER_TYPE,
          'slug' => 'work',
          'img_src' => 'src/images/site/nav/work.png',
          'nav_string' => 'Work',
          'pageid' => 2,
          'orderby' => 2
         ],
         ['navid' => 8,
          'type' => self::FOOTER_TYPE,
          'slug' => 'robots',
          'img_src' => 'src/images/site/nav/robots.png',
          'nav_string' => 'Robots',
          'pageid' => 3,
          'orderby' => 3
         ],
         ['navid' => 9,
          'type' => self::FOOTER_TYPE,
          'slug' => 'life',
          'img_src' => 'src/images/site/nav/life.png',
          'nav_string' => 'Life',
          'pageid' => 4,
          'orderby' => 4
         ]
      ];
      return array_map(fn($vals) => self::fromArray($vals), $values);
   }
}
