<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;

/**
 * The "nav" element has a bunch of links to urls & lives in the
 * "body" element.
 */
class Nav extends HtmlElement {
   protected $navData;
   private const FRAMEWORK_FILE = 'nav.phtml';

   public function __construct(array $pageIndexRows) {
      $this->navData = $this->extractNavDataFromPageIndexRows($pageIndexRows);
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   private function extractNavDataFromPageIndexRows(array $pageIndexRows): array {
      $navData = [];
      foreach ($pageIndexRows as $row) {
         $navData[] = [
            'url' => "/{$row['slug']}",
            'display' => $row['nav_string']
         ];
      }
      return $navData;
   }
}
