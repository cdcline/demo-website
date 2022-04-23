<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;

/**
 * Each page has a large header with text; this is the element
 * that prints it out.
 *
 * The actual text is created in the Page objects.
 */
class Header extends HtmlElement {
   private $headerText;
   private const FRAMEWORK_FILE = 'header.phtml';

   public function __construct(string $headerText) {
      $this->headerText = $headerText;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function getHeaderText(): string {
      return $this->headerText;
   }
}
