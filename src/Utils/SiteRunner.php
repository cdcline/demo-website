<?php declare(strict_types=1);

namespace Utils;

use Pages\PageCollection;
use Pages\BasePage;

class SiteRunner {
   public static function runPage(): void {
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

   private static function getPageFromUrl(): BasePage {
      return PageCollection::getPageFromSlug(self::getSlugFromUrl());
   }
}
