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
      $iPageValues = array_map(fn($iPage) => $iPage->toArray(), PageIndex::fetchAllRowsFromStaticCache());
      $tPageData = StringUtils::filterArrayByKeys($iPageValues, $iPageTable);
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
      $pageLists = PageLists::fetchAll();
      if ($pageLists) {
         $pageList = current($pageLists);
         $maTitle = $pageList['title'];
         $pageArticles = $pageList['articles'];
         $convertArticles = function(&$article) {
            $article['tags'] = implode(',', $article['tags']);
            return $article;
         };
         $pageArticles = array_map($convertArticles, $pageArticles);
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
}
