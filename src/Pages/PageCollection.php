<?php declare(strict_types=1);

namespace Pages;

use DB\PageNav;
use DB\PageIndex;
use Utils\StringUtils;
use Pages\BasePage;
use Pages\AboutMePage;
use Pages\DevPage;

class PageCollection {
   private $pageid;

   public static function getPageFromSlug(string $slug): BasePage {
      $pageid = StringUtils::isInt($slug) ? (int)$slug : PageNav::getPageidFromSlug($slug);
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

   private function getClassFromPageType() {
      foreach ($this->getPageTypes() as $pageType) {
         if ($pageType::matchesType($this->pageType)) {
            return $pageType;
         }
      }
   }

   private function getPageTypes(): array {
      return [AboutMePage::class, DevPage::class];
   }
}
