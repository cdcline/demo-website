<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Body as HtmlBody;
use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Head as HtmlHead;
use HtmlFramework\Packet\RootPacket;

/**
 * The "root" element is where we have the outmost html layer defined.
 *
 * It should be the first and list text printed to the user.
 */
class Root extends HtmlElement {
   private const FRAMEWORK_FILE = 'root.phtml';

   public static function fromValues(HtmlHead $head, HtmlBody $body): self {
      $packet = new RootPacket($head, $body);
      return new self($packet);
   }

   public function __construct(RootPacket $packet) {
      $this->packet = $packet;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }
}
