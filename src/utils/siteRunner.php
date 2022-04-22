<?php declare(strict_types=1);

namespace Utils;

use Utils\Server as ServerUtils;
use Utils\Page as Page;

class SiteRunner {
   public static function runPage() {
      $txt = ServerUtils::onLiveSite() ? 'live' : 'dev';
      echo "<h1>{$txt}</h1>";
      echo "hello world!\n";
      Page::display();
      die();
   }
}
