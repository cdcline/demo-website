<?php declare(strict_types=1);

namespace Pages;

use Pages\BasePage;

class DevPage extends BasePage {
   private const PAGE_TITLE = 'Dev Page';
   private const PAGE_TEMPLATE = 'dev.phtml';

   protected function getPageTitle(): string {
      return self::PAGE_TITLE;
   }

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }
}