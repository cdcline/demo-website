<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Packet\HeadPacket;

/**
 * The "head" element is a bit confusing because it's full of things that
 * the browser uses for all sorts of random stuff.
 *
 * It's not seen by the user and goes above the "body" element.
 */
class Head extends HtmlElement {
   private $pageTitle;
   private const FRAMEWORK_FILE = 'head.phtml';

   public static function fromValues(string $pageTitle): self {
      $packet = new HeadPacket($pageTitle);
      return new self($packet);
   }

   private function __construct(HeadPacket $packet) {
      $this->packet = $packet;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function getPageTitle(): string {
      return $this->pageTitle;
   }

}
