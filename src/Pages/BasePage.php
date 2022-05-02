<?php declare(strict_types=1);

namespace Pages;

use DB\PageIndex;
use DB\PageNav;
use HtmlFramework\Article as HtmlArticle;
use HtmlFramework\Body as HtmlBody;
use HtmlFramework\Footer as HtmlFooter;
use HtmlFramework\Head as HtmlHead;
use HtmlFramework\Header as HtmlHeader;
use HtmlFramework\Nav as HtmlNav;
use HtmlFramework\Root as HtmlRoot;
use HtmlFramework\Section as HtmlSection;
use Utils\StringUtils;

abstract class BasePage {
   private const TEMPLATE_PATH = 'src/templates';
   private $pageid;
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
      $htmlNav = HtmlNav::fromValues();
      $htmlArticle = HtmlArticle::fromValues($this->getPageid(), $this->getPageTemplatePath(), $this->pageData, $this->getMainArticle());
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

      return $this->pageIndexRows = PageIndex::fetchAllRows();
   }

   private function getRowByPageid(int $pageid): array {
      foreach ($this->getPageIndexRows() as $row) {
         if ($pageid == $row['pageid']) {
            return $row;
         }
      }
      return [];
   }

   /**
    * We'd like to support multiple pages: https://github.com/cdcline/demo-website/issues/32
    * but that requires more logic to abstract "templates" from "slugs" and we're
    * not there yet.
    *
    * I'd still like to start using `pageid` for all the logic ASAP so we'll
    * stick this little hack in until pages are made with an id & type.
    */
   private function getPageid(): int {
      if (isset($this->pageid)) {
         return $this->pageid;
      }

      return $this->pageid = PageNav::getPageidFromSlug($this->getPageSlug());
   }

   private function getPageTitle(): string {
      return $this->getRowByPageid($this->getPageid())['page_title'];
   }

   private function getPageHeader(): string {
      return $this->getRowByPageid($this->getPageid())['page_header'];
   }

   private function getMainArticle(): string {
      return $this->getRowByPageid($this->getPageid())['main_article'];
   }

   private function getPageTemplatePath(): string {
      return self::TEMPLATE_PATH . "/{$this->getPageTemplateName()}";
   }
}
