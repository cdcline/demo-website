<?php declare(strict_types=1);

namespace Pages;

use HtmlFramework\Article as HtmlArticle;
use HtmlFramework\Body as HtmlBody;
use HtmlFramework\Footer as HtmlFooter;
use HtmlFramework\Head as HtmlHead;
use HtmlFramework\Header as HtmlHeader;
use HtmlFramework\Nav as HtmlNav;
use HtmlFramework\Root as HtmlRoot;
use HtmlFramework\Section as HtmlSection;
use DB\PDOConnection;
use Utils\StringUtils;

abstract class BasePage {
   private const TEMPLATE_PATH = 'src/templates';
   private $pageData = [];
   private $pageIndexRows;

   // Name of the file we'll load in the "article" section.
   abstract protected function getPageTemplateName(): string;
   // The "Page Slug" is what we match in the url
   abstract protected function getPageSlug(): string;

   // Before we print the page we might want to do stuff.
   public function doStuff(): void {}

   public function setPageData(string $index, $value): void {
      $this->pageData[$index] = $value;
   }

   public function matchesSlug(string $slug) {
      return StringUtils::iMatch($this->getPageSlug(), $slug);
   }

   public function printHtml(): void {
      $htmlHead = HtmlHead::fromValues($this->getPageTitle());
      $htmlHeader = HtmlHeader::fromValues($this->getPageHeader());
      $htmlNav = HtmlNav::fromValues($this->getPageIndexRows());
      $htmlArticle = HtmlArticle::fromValues($this->getPageTemplatePath(), $this->pageData, $this->getMainArticle());
      $htmlSection = HtmlSection::fromValues($htmlNav, $htmlArticle);
      $htmlFooter = HtmlFooter::fromValues();
      $htmlBody = HtmlBody::fromValues($htmlHeader, $htmlSection, $htmlFooter);
      $htmlRoot = HtmlRoot::fromValues($htmlHead, $htmlBody);
      $htmlRoot->printHtml();
   }

   private function getPageIndexRows(): array {
      if (isset($this->pageIndexRows)) {
         return $this->pageIndexRows;
      }

      return $this->pageIndexRows = PDOConnection::fetchPageIndexData();
   }

   private function getRowBySlug(string $slug): array {
      foreach ($this->getPageIndexRows() as $row) {
         if (StringUtils::iMatch($row['slug'], $slug)) {
            return $row;
         }
      }
      return [];
   }

   private function getPageTitle(): string {
      return $this->getRowBySlug($this->getPageSlug())['page_title'];
   }

   private function getPageHeader(): string {
      return $this->getRowBySlug($this->getPageSlug())['page_header'];
   }

   private function getMainArticle(): string {
      return $this->getRowBySlug($this->getPageSlug())['main_article'];
   }

   private function getPageTemplatePath(): string {
      return self::TEMPLATE_PATH . "/{$this->getPageTemplateName()}";
   }
}
