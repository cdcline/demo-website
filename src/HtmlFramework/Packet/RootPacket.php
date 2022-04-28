<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Head as HtmlHead;
use HtmlFramework\Body as HtmlBody;

use HtmlFramework\Packet\PacketTrait;

class RootPacket {
   use PacketTrait;

   public function __construct(HtmlHead $head, HtmlBody $body) {
      $this->setData('head', $head);
      $this->setData('body', $body);
   }
}
