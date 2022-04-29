<?php declare(strict_types=1);

namespace HtmlFramework;

use DB\PageNav;
use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Packet\NavPacket;

/**
 * The "nav" element has a bunch of links to urls & lives in the
 * "body" element.
 */
class Nav extends HtmlElement {
   protected $navData;
   private const FRAMEWORK_FILE = 'nav.phtml';

   public static function fromValues(): self {
      $navPacket = new NavPacket(PageNav::fetchAllRows());
      return new self($navPacket);
   }

   private function __construct(NavPacket $packet) {
      $this->packet = $packet;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }
}
