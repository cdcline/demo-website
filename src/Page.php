<?php declare(strict_types=1);

namespace Utils;

use Utils\Server as ServerUtils;

class Page {
   private const TEMPLATE_PATH = 'src/templates';

   private $pageData;
   private $pageName;

   public static function display(): void {
      $page = new self();
      $page->setPageData('foo', ServerUtils::onLiveSite() ? 'Live' : 'Dev');
      $page->echoHtml();
   }

   public function __construct() {
      $this->pageName = 'page.phtml';
   }

   private function setPageData(string $index, $value): void {
      $this->pageData[$index] = $value;
   }

   private function getPageTitle(): string {
      return 'Demo Page';
   }

   private function echoHtml(): void {
      require 'src/templates/page.phtml';
   }

   private function getStyleSheetPath(): string {
      return 'src/templates/css/page.css';
   }

   private function getJavascriptPath(): string {
      return 'src/templates/js/page.js';
   }
}
