<?php declare(strict_types=1);

namespace Pages;

use Exception;
use Pages\BasePage;
use DB\PDOConnection;
use Utils\Server as ServerUtils;
use Utils\SecretManager;
use Parsedown;

class DevPage extends BasePage {
   private const PAGE_SLUG = 'dev';
   private const PAGE_TEMPLATE = 'dev.phtml';

   public function doStuff(): void {
      try {
         $secret = SecretManager::spoilSecret();
      } catch (Exception $e) {
         $secret = $e->getMessage();
      }
      $secret = "_{$secret}_";
      $parser = new Parsedown();
      $secret = $parser->text($secret);
      $this->setPageData('test', $secret);
      $this->setPageData('pageInfo', PDOConnection::fetchPageIndexData());
   }

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }

   protected function getPageSlug(): string {
      return self::PAGE_SLUG;
   }
}
