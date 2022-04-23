<?php declare(strict_types=1);

namespace Pages;

use Pages\BasePage;

class AboutMePage extends BasePage {
   private const PAGE_TITLE = 'Demo Page';
   private const PAGE_TEMPLATE = 'page.phtml';

   protected function getPageTitle(): string {
      return self::PAGE_TITLE;
   }

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }
}