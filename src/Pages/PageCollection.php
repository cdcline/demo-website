<?php declare(strict_types=1);

namespace Pages;

use DB\PageNav;
use DB\PageIndex;
use Utils\StringUtils;
use Pages\BasePage;
use Pages\HomePage;
use Pages\DefaultPage;
use Pages\DevPage;

/**
 * Basic object to setup and return a Page off of a "$slug"
 *  - Keeps track of all "Page Types"
 *  - Returns a Page of the correct Type for a given $pageid
 */
class PageCollection {
   private $pageid;

   /**
    * We call the shortened url a "slug" and here we figure out the pageid off
    * of the slug.
    */
   public static function getPageFromSlug(string $slug): BasePage {
      $pageid = PageNav::DEFAULT_PAGEID;
      if ($slug) {
         // If it's an int looking string, assume we want to load by pageid else lookup a pageid from page_nav.slug
         $pageid = StringUtils::isInt($slug) ? (int)$slug : PageNav::getPageidFromSlug($slug);
      }
      return (new self($pageid))->getPage();
   }

   private function __construct($pageid) {
      $this->pageid = $pageid;
      $this->pageType = PageIndex::getTypeFromPageid($pageid);
   }

   private function getPage(): BasePage {
      $pClass = $this->getClassFromPageType();
      return new $pClass($this->pageid);
   }

   private function getClassFromPageType(): string {
      foreach ($this->getPageTypes() as $pageType) {
         if ($pageType::matchesType($this->pageType)) {
            return $pageType;
         }
      }
   }

   private function getPageTypes(): array {
      return [DefaultPage::class, HomePage::class, DevPage::class];
   }
}
