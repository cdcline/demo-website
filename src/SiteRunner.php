<?php declare(strict_types=1);

namespace Utils;

use Pages\AboutMePage as AboutMePage;
use Pages\DevPage as DevPage;
use Utils\Server as ServerUtils;

class SiteRunner {
   public static function runPage() {
      $page = self::getPageFromUrl();
      $page->printHtml();
   }

   private static function getPageFromUrl() {
      // Parse it just b/c we might as well
      $urlParts = parse_url($_SERVER['REQUEST_URI']);
      $path = $urlParts['path'];
      // Lop off the leading "/" from the path
      $urlKey = substr($path, 1);
      // Pick out one of our few extra pages
      switch (strtolower($urlKey)) {
         case 'dev':
            return new DevPage();
      }
      // Otherwise show our default page
      $page = new AboutMePage();
      // TODO: delete me - Adding this just to make sure things are assigning in the template
      $page->setPageData('foo', ServerUtils::onLiveSite() ? 'Live' : 'Dev');
      return $page;
   }
}
