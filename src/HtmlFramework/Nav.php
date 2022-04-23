<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;

/**
 * The "nav" element has a bunch of links to urls & lives in the
 * "body" element.
 */
class Nav extends HtmlElement {
   private const FRAMEWORK_FILE = 'nav.phtml';

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }
}
