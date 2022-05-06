<?php declare(strict_types=1);

namespace Utils;

use Pages\PageCollection;
use Pages\BasePage;
use DB\PageNav;

class SiteRunner {
   private static $slug;

   public static function runPage(): void {
      $page = self::getPageFromUrl();
      $page->doStuff();
      $page->printHtml();
   }

   public static function getSlugFromUrl(): string {
      if (!is_null(self::$slug)) {
         return self::$slug;
      }
      // Parse it just b/c we might as well
      $urlParts = parse_url($_SERVER['REQUEST_URI']);
      $path = $urlParts['path'];
      // Lop off the leading "/" from the path
      return self::$slug = substr($path, 1) ?: PageNav::getDefaultSlug();
   }

   private static function getPageFromUrl(): BasePage {
      return PageCollection::getPageFromSlug(self::getSlugFromUrl());
   }
}
