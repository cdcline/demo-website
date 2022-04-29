<?php declare(strict_types=1);

namespace Pages;

use Exception;
use Pages\BasePage;
use DB\MiniArticle;
use DB\PageIndex;
use Utils\Server as ServerUtils;
use Utils\SecretManager;
use Utils\StringUtils;
use Utils\HtmlUtils;
use Parsedown;

class DevPage extends BasePage {
   private const PAGE_SLUG = 'dev';
   private const PAGE_TEMPLATE = 'dev.phtml';

   public function doStuff(): void {
      $this->setPageData('pageTableHtml', HtmlUtils::makeTableElement($this->getPageIndexTableData()));
      $this->setPageData('maTableHtml', HtmlUtils::makeTableElement($this->getMiniArticleTableData()));
      $this->setPageData('testSecret', $this->testParsedown($this->getTestSecret()));
   }

   private function testParsedown(string $rawText): string {
      $parser = new Parsedown();
      return $parser->line($rawText);
   }

   private function getTestSecret(): string {
      try {
         $secret = SecretManager::spoilSecret();
      } catch (Exception $e) {
         $secret = $e->getMessage();
      }
      // secretly it's a parsedown secret
      return "_{$secret}_";
   }

   private function getPageIndexTableData(): array {
      // Page Index Table
      $tPageHeader = ['Pageid', 'Page Title', 'Page Header'];
      $iPageTable = ['pageid', 'page_title', 'page_header'];
      $tPageData = StringUtils::filterArrayByKeys(PageIndex::fetchAllRows(), $iPageTable);
      return [
         'caption' => 'Page Index Rows',
         'header' => $tPageHeader,
         'rows' => $tPageData
      ];
   }

   private function getMiniArticleTableData(): array {
      $maHeader = ['Title', 'Start Date', 'End Date', 'Tags'];
      $iMiniArticleTable = ['title', 'start_date', 'end_date', 'tags'];
      $maData = StringUtils::filterArrayByKeys(MiniArticle::fetchAll(/*breakUpConcat*/false), $iMiniArticleTable);
      return [
         'caption' => 'Mini Article Rows',
         'header' => $maHeader,
         'rows' => $maData
      ];
   }

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }

   protected function getPageSlug(): string {
      return self::PAGE_SLUG;
   }
}
