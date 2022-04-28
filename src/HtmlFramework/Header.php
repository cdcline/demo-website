<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Packet\HeaderPacket;

/**
 * Each page has a large header with text; this is the element
 * that prints it out.
 *
 * The actual text is created in the Page objects.
 */
class Header extends HtmlElement {
   private $headerText;
   private const FRAMEWORK_FILE = 'header.phtml';

   public static function fromValues(string $headerText): self {
      $packet = new HeaderPacket($headerText);
      return new self($packet);
   }

   private function __construct(HeaderPacket $packet) {
      $this->packet = $packet;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function getHeaderText(): string {
      return $this->headerText;
   }
}
