<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Packet\PacketTrait;

class HeaderPacket {
   use PacketTrait;

   public function __construct(string $headerText, ?string $headerImg) {
      $this->setData('headerText', $headerText);
      $this->setData('headerImgSrc', $headerImg);
   }
}
