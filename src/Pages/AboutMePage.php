<?php declare(strict_types=1);

namespace Pages;

use Pages\BasePage;
use Utils\Server as ServerUtils;

class AboutMePage extends BasePage {
   private const PAGE_TITLE = 'Demo Page';
   private const PAGE_TEMPLATE = 'page.phtml';

   public static function display(): void {
      $page = new self();
      $page->setPageData('foo', ServerUtils::onLiveSite() ? 'Live' : 'Dev');
      $page->printHtml();
   }

   protected function getPageTitle(): string {
      return self::PAGE_TITLE;
   }

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }
}