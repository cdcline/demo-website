<?php declare(strict_types=1);

namespace Pages;

use DB\MiniArticleList\PageLists;
use DB\PageIndex;
use Exception;
use Pages\BasePage;
use Utils\HtmlUtils;
use Utils\Parser;
use Utils\SecretManager;
use Utils\StringUtils;

final class DevPage extends BasePage {
   private const PAGE_TEMPLATE = 'dev.phtml';

   public function doStuff(): void {
      $this->setPageData('pageTableHtml', HtmlUtils::makeTableElement($this->getPageIndexTableData()));
      $this->setPageData('maTableHtml', HtmlUtils::makeTableElement($this->getMiniArticleTableData()));
      $this->setPageData('testSecret', $this->testParsedown($this->getTestSecret()));
   }

   private function testParsedown(string $rawText): string {
      return Parser::parseLine($rawText);
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
      $tPageHeader = ['Type', 'Pageid', 'Page Title', 'Page Header'];
      $iPageTable = ['type', 'pageid', 'page_title', 'page_header'];
      $tPageData = StringUtils::filterArrayByKeys(PageIndex::fetchAllRowsFromStaticCache(), $iPageTable);
      return [
         'caption' => 'Page Index Rows',
         'header' => $tPageHeader,
         'rows' => $tPageData
      ];
   }

   private function getMiniArticleTableData(): array {
      $maHeader = ['Title', 'Start Date', 'End Date', 'Tags'];
      $maTitle = 'Mini Article Rows';
      $maData = [];
      $iMiniArticleTable = ['title', 'start_date', 'end_date', 'tags'];
      $pageLists = PageLists::fetchAll(/*tagsAsOneStr*/true);
      if ($pageLists) {
         $pageList = current($pageLists);
         $maTitle = $pageList['title'];
         $pageArticles = $pageList['articles'];
         $maData = StringUtils::filterArrayByKeys($pageArticles, $iMiniArticleTable);
      }
      return [
         'caption' => $maTitle,
         'header' => $maHeader,
         'rows' => $maData
      ];
   }

   protected function getPageTemplateName(): string {
      return self::PAGE_TEMPLATE;
   }

   protected static function getPageType(): string {
      return PageIndex::DEV_TYPE;
   }
}
