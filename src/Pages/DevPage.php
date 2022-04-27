<?php declare(strict_types=1);

namespace Pages;

use Exception;
use Pages\BasePage;
use Utils\DB;
use Utils\Server as ServerUtils;
use Utils\SecretManager;

class DevPage extends BasePage {
   private const PAGE_SLUG = 'dev';
   private const PAGE_TITLE = 'Dev Page';
   private const PAGE_TEMPLATE = 'dev.phtml';

   public function doStuff(): void {
      try {
         $secret = SecretManager::spoilSecret();
      } catch (Exception $e) {
         $secret = $e->getMessage();
      }
      $this->setPageData('test', $secret);
      $this->setPageData('pageInfo', DB::fetchPageIndexData());
   }

   protected function getPageTitle(): string {
      return self::PAGE_TITLE;
   }

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }

   protected function getPageSlug(): string {
      return self::PAGE_SLUG;
   }

   protected function getPageHeader(): string {
      $fName = ServerUtils::onLiveSite() ? 'Live' : 'Dev';
      return "My {$fName} Test Site";
   }
}
