<?php declare(strict_types=1);

namespace Pages;

use DB\PageIndex;
use HtmlFramework\Article as HtmlArticle;
use HtmlFramework\Body as HtmlBody;
use HtmlFramework\Footer as HtmlFooter;
use HtmlFramework\Head as HtmlHead;
use HtmlFramework\Header as HtmlHeader;
use HtmlFramework\Nav as HtmlNav;
use HtmlFramework\Root as HtmlRoot;
use HtmlFramework\Section as HtmlSection;
use Pages\InvalidPageException;

/**
 * This object associates `page_index.type` with:
 *  - Some arbitrary set of display logic: doStuff()
 *  - Some arbitrary set of display structure: printHtml()
 *  - Some arbitrary set of data: $pageData
 *
 * If you ever want one Page to do something different than another or you
 * want all the Pages to do the thing, this is the place to start.
 */
abstract class BasePage {
   private const TEMPLATE_PATH = 'src/templates';
   private $ranHTMLPrint = false;
   private $pageData = [];
   private $pageIndex;

   // Name of the file we'll load in the "article" section.
   abstract protected function getPageTemplateName(): string;
   // Before we print the page we might want to do stuff.
   public function doStuff(): void {}

   public function __construct(PageIndex $pageIndex) {
      $this->pageIndex = $pageIndex;
   }

   public function getPageid(): int {
      return $this->getPageIndex()->getPageid();
   }

   public function setPageData(string $index, $value): void {
      if ($this->ranHTMLPrint) {
         InvalidPageException::throwInvalidPageOperation("Please don't set page data after printing it. Logic will become a crazy mess!");
      }
      $this->pageData[$index] = $value;
   }

   public function printHtml(): void {
      $this->ranHTMLPrint = true;
      $htmlHead = HtmlHead::fromValues($this->getTheme(), $this->getPageTitle());
      $htmlHeader = HtmlHeader::fromValues($this->getPageHeader());
      $htmlNav = HtmlNav::fromValues();
      $htmlArticle = HtmlArticle::fromValues($this->getPageType(), $this->getPageid(), $this->getPageTemplatePath(), $this->pageData, $this->getMainArticle());
      $htmlFooter = HtmlFooter::fromValues($this->getNavText());
      $htmlSection = HtmlSection::fromValues($htmlArticle, $htmlNav, $htmlFooter);
      $htmlBody = HtmlBody::fromValues($htmlHeader, $htmlSection, $htmlFooter);
      $htmlRoot = HtmlRoot::fromValues($htmlHead, $htmlBody);
      $htmlRoot->printHtml();
   }

   public static function getClassNameFromPageType(string $pageType): string {
      switch ($pageType) {
         case PageIndex::DEV_TYPE:
            return DevPage::class;
         case PageIndex::HOMEPAGE_TYPE:
            return HomePage::class;
         case PageIndex::DEFAULT_TYPE:
            return DefaultPage::class;
      }
      throw new InvalidPageException("Unable to find Page Type: {$pageType}");
   }

   protected function getMainArticle(): string {
      return $this->getPageIndex()->getMainArticle();
   }

   private function getPageIndex(): PageIndex {
      return $this->pageIndex;
   }

   private function getTheme(): string {
      return $this->getPageIndex()->getTheme();

   }

   private function getPageType(): string {
      return $this->getPageIndex()->getPageType();
   }

   private function getPageTitle(): string {
      return $this->getPageIndex()->getPageTitle();
   }

   private function getPageHeader(): string {
      return $this->getPageIndex()->getPageHeader();
   }

   private function getNavText(): string {
      return $this->getPageIndex()->getNavText();
   }

   private function getPageTemplatePath(): string {
      return self::TEMPLATE_PATH . "/{$this->getPageTemplateName()}";
   }
}
