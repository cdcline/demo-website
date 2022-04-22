<?php declare(strict_types=1);

namespace Utils;

use Utils\Page as Page;

class SiteRunner {
   public static function runPage() {
      Page::display();
   }
}
