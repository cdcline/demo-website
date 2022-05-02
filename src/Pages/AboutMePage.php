<?php declare(strict_types=1);

namespace Pages;

use Pages\BasePage;
use Utils\ServerUtils;

class AboutMePage extends BasePage {
   private const PAGE_SLUG = 'about-me';
   private const PAGE_TEMPLATE = 'about_me.phtml';

   protected function getPageSlug(): string {
      return self::PAGE_SLUG;
   }

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }
}
