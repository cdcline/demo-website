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
use Utils\StringUtils;
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
   private $pageid;
   private $pageData = [];
   private $pageIndexRows;

   // Name of the file we'll load in the "article" section.
   abstract protected function getPageTemplateName(): string;
   // Before we print the page we might want to do stuff.
   public function doStuff(): void {}

   public function __construct(int $pageid) {
      $this->pageid = $pageid;
   }

   public function setPageData(string $index, $value): void {
      if ($this->ranHTMLPrint) {
         InvalidPageException::throwInvalidPageOperation("Please don't set page data after printing it. Logic will become a crazy mess!");
      }
      $this->pageData[$index] = $value;
   }

   public static function matchesType(string $type) {
      return StringUtils::iMatch(static::getPageType(), $type);
   }

   public function printHtml(): void {
      $this->ranHTMLPrint = true;
      $htmlHead = HtmlHead::fromValues($this->getPageTitle());
      $htmlHeader = HtmlHeader::fromValues($this->getPageHeader());
      $htmlNav = HtmlNav::fromValues();
      $htmlArticle = HtmlArticle::fromValues(static::getPageType(), $this->getPageid(), $this->getPageTemplatePath(), $this->pageData, $this->getMainArticle());
      $htmlSection = HtmlSection::fromValues($htmlNav, $htmlArticle);
      $htmlFooter = HtmlFooter::fromValues();
      $htmlBody = HtmlBody::fromValues($htmlHeader, $htmlSection, $htmlFooter);
      $htmlRoot = HtmlRoot::fromValues($htmlHead, $htmlBody);
      $htmlRoot->printHtml();
   }

   protected static function getPageType(): string {
      return PageIndex::DEFAULT_TYPE;
   }

   private function getPageIndexRows(): array {
      if (isset($this->pageIndexRows)) {
         return $this->pageIndexRows;
      }

      return $this->pageIndexRows = PageIndex::fetchAllRowsFromStaticCache();
   }

   private function getRowByPageid(int $pageid): array {
      foreach ($this->getPageIndexRows() as $row) {
         if ($pageid == $row['pageid']) {
            return $row;
         }
      }
      return [];
   }

   protected function getPageid(): int {
      return $this->pageid;
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
