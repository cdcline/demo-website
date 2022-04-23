<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;

class Footer extends HtmlElement {
   private const FRAMEWORK_FILE = 'footer.phtml';

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }
}
