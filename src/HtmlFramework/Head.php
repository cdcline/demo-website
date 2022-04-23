<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;

/**
 * The "head" element is a bit confusing because it's full of things that
 * the browser uses for all sorts of random stuff.
 *
 * It's not seen by the user and goes above the "body" element.
 */
class Head extends HtmlElement {
   private $pageTitle;
   private const FRAMEWORK_FILE = 'head.phtml';
   private const HTML_STYLE_SHEET_PATH = 'src/templates/css/page.css';

   public function __construct(string $pageTitle) {
      $this->pageTitle = $pageTitle;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function getPageTitle(): string {
      return $this->pageTitle;
   }

   protected function getStyleSheetPath(): string {
      return self::HTML_STYLE_SHEET_PATH;
   }
}
