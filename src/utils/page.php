<?php declare(strict_types=1);

namespace Utils;

class Page {
   private const TEMPLATE_PATH = 'src/templates';

   private array $pageData;
   private string $pageName;

   public static function display(): void {
      $page = new self();
      $page->setPageData('zoo', 'foo');
      $page->setPageData('boo', 'bar');
      $page->echoHtml();
   }

   public function __construct() {
      $this->pageName = 'page.phtml';
   }

   private function setPageData(string $index, mixed $value): void {
      $this->pageData[$index] = $value;
   }

   private function echoHtml(): void {
      $zoo = 'foo';
      require 'src/templates/page.phtml';
   }

}
