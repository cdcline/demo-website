<?php declare(strict_types=1);

namespace Pages;

use Pages\BasePage;
use Utils\Server as ServerUtils;

class AboutMePage extends BasePage {
   private const PAGE_SLUG = 'about-me';
   private const PAGE_TITLE = 'Demo Page';
   private const PAGE_TEMPLATE = 'about_me.phtml';

   protected function getPageTitle(): string {
      return self::PAGE_TITLE;
   }

   protected function getPageHeader(): string {
      $fName = ServerUtils::onLiveSite() ? 'Live' : 'Dev';
      return "My {$fName} Demo Site";
   }

   protected function getPageSlug(): string {
      return self::PAGE_SLUG;
   }

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }
}