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
use Utils\DB;

abstract class BasePage {
   private const TEMPLATE_PATH = 'src/templates';
   private $pageData = [];
   private $pageIndexRows;

   // Name of the file we'll load in the "article" section.
   abstract protected function getPageTemplateName(): string;
   // The "Title" of the page is the meta title
   abstract protected function getPageTitle(): string;
   // The "Page Header" is what will show up on each page
   abstract protected function getPageHeader(): string;

   // Before we print the page we might want to do stuff.
   public function doStuff(): void {}

   public function setPageData(string $index, $value): void {
      $this->pageData[$index] = $value;
   }

   public function printHtml(): void {
      $htmlHead = new HtmlHead($this->getPageTitle());
      $htmlHeader = new HtmlHeader($this->getPageHeader());
      $htmlNav = HtmlNav::fromPageIndexRows($this->getPageIndexRows());
      $htmlArticle = HtmlArticle::fromValues($this->getPageTemplatePath(), $this->pageData);
      $htmlSection = new HtmlSection($htmlNav, $htmlArticle);
      $htmlFooter = new HtmlFooter();
      $htmlBody = new HtmlBody($htmlHeader, $htmlSection, $htmlFooter);
      $htmlRoot = new HtmlRoot($htmlHead, $htmlBody);
      $htmlRoot->printHtml();
   }

   private function getPageIndexRows(): array {
      if (isset($this->pageIndexRows)) {
         return $this->pageIndexRows;
      }

      return $this->pageIndexRows = DB::fetchPageIndexData();
   }

   private function getPageTemplatePath(): string {
      return self::TEMPLATE_PATH . "/{$this->getPageTemplateName()}";
   }
}
