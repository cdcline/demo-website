<?php declare(strict_types=1);

namespace Utils;

use Pages\BasePage;
use DB\PageNav;

class SiteRunner {
   private static $slug;

   public static function runPage(): void {
      $slug = self::getSlugFromUrl();
      if ($redirectData = PageNav::getRedirectDataFromSlug($slug)) {
         ServerUtils::printRedirect(
           $redirectData['url'],
           $redirectData['timeout'] ?? 0,
           $redirectData['title'] ?? null,
           $redirectData['image'] ?? null);
         return;
      }
      $page = self::getPageFromUrl($slug);
      $page->doStuff();
      $page->printHtml();
   }

   public static function getSlugFromUrl(): string {
      if (!is_null(self::$slug)) {
         return self::$slug;
      }
      // Parse it just b/c we might as well
      $urlParts = parse_url($_SERVER['REQUEST_URI']);
      $path = urldecode($urlParts['path']);
      // Lop off the leading "/" from the path
      return self::$slug = substr($path, 1) ?: PageNav::getDefaultSlug();
   }

   private static function getPageFromUrl(string $slug): BasePage {
      return PageNav::getPageFromSlug($slug);
   }
}
