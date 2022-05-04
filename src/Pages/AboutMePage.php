<?php declare(strict_types=1);

namespace Pages;

use Pages\BasePage;
use DB\PageIndex;

final class AboutMePage extends BasePage {
   private const PAGE_TEMPLATE = 'about_me.phtml';

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }

   protected static function getPageType(): string {
      return PageIndex::ABOUT_ME_TYPE;
   }
}
