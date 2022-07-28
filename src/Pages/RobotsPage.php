<?php declare(strict_types=1);

namespace Pages;

use Pages\BasePage;

final class RobotsPage extends BasePage {
   private const PAGE_TEMPLATE = 'robots.phtml';

   public function doStuff(): void { }

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }
}

