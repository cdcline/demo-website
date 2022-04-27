<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Packet\FooterPacket;

class Footer extends HtmlElement {
   private const FRAMEWORK_FILE = 'footer.phtml';

   public static function fromValues(): self {
      $packet = new FooterPacket();
      return new self($packet);
   }

   private function __construct(FooterPacket $packet) {
      $this->packet = $packet;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }
}
