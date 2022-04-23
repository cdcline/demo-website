<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Head as HtmlHead;
use HtmlFramework\Body as HtmlBody;

/**
 * The "root" element is where we have the outmost html layer defined.
 *
 * It should be the first and list text printed to the user.
 */
class Root extends HtmlElement {
   protected $head;
   protected $body;

   private const FRAMEWORK_FILE = 'root.phtml';

   public function __construct(HtmlHead $head, HtmlBody $body) {
      $this->head = $head;
      $this->body = $body;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }
}
