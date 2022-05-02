<?php declare(strict_types=1);

namespace Pages;

use Pages\BasePage;

final class DefaultPage extends BasePage {
   private const PAGE_TEMPLATE = 'default.phtml';

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }
}
