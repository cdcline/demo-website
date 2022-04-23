<?php declare(strict_types=1);

namespace Utils;

use Pages\AboutMePage as Page;

class SiteRunner {
   public static function runPage() {
      Page::display();
   }
}
