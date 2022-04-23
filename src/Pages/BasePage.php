<?php declare(strict_types=1);

namespace Pages;

use Utils\Server as ServerUtils;

abstract class BasePage {
   private const TEMPLATE_PATH = 'src/templates';
   private $pageData;

   abstract protected function getPageTemplateName(): string;
   abstract protected function getPageTitle(): string;

   protected function setPageData(string $index, $value): void {
      $this->pageData[$index] = $value;
   }

   protected function printHtml(): void {
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
