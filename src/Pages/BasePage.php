<?php declare(strict_types=1);

namespace Pages;

abstract class BasePage {
   private const TEMPLATE_PATH = 'src/templates';
   private $pageData;

   abstract protected function getPageTemplateName(): string;
   abstract protected function getPageTitle(): string;

   public function setPageData(string $index, $value): void {
      $this->pageData[$index] = $value;
   }

   public function printHtml(): void {
      $templatePath = self::TEMPLATE_PATH . "/{$this->getPageTemplateName()}";
      require $templatePath;
   }

   protected function getStyleSheetPath(): string {
      return 'src/templates/css/page.css';
   }

   protected function getJavascriptPath(): string {
      return 'src/templates/js/page.js';
   }
}
