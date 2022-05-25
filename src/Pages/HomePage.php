<?php declare(strict_types=1);

namespace Pages;

use Pages\BasePage;
use DB\PageIndex;

final class HomePage extends BasePage {
   private const PAGE_TEMPLATE = 'homepage.phtml';

   public function doStuff(): void {
      $this->setPageData('escapedMainArticleTxt', htmlentities(PageIndex::getMainArticleTextFromPageid($this->getPageid())));
   }

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }
}
