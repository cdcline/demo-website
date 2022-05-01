<?php declare(strict_types=1);

namespace Utils;

use Pages\AboutMePage as AboutMePage;
use Pages\DevPage as DevPage;
use Utils\ServerUtils;

class SiteRunner {
   public static function runPage() {
      $page = self::getPageFromUrl();
      $page->doStuff();
      $page->printHtml();
   }

   private static function getSlugFromUrl(): string {
      // Parse it just b/c we might as well
      $urlParts = parse_url($_SERVER['REQUEST_URI']);
      $path = $urlParts['path'];
      // Lop off the leading "/" from the path
      return substr($path, 1);
   }

   private static function getDefaultPage(): AboutMePage {
      $aboutMeClass = AboutMePage::class;
      return new $aboutMeClass;
   }

   private static function getAllButDefaultPages(): array {
      $devPageClass = DevPage::class;
      $devPage = new $devPageClass;
      return [$devPage];
   }

   private static function getPageFromUrl() {
      $slug = self::getSlugFromUrl();
      foreach (self::getAllButDefaultPages() as $page) {
         if ($page->matchesSlug($slug)) {
            return $page;
         }
      }
      return self::getDefaultPage();
   }
}
