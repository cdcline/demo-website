<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Head as HtmlHead;
use HtmlFramework\Body as HtmlBody;

use HtmlFramework\Packet\PacketTrait;

class FooterPacket {
   private const CONTACT_EMAIL = 'mailto:1248182+cdcline@users.noreply.github.com';

   use PacketTrait;

   public function __construct() {
      $this->setData('contactEmail', self::CONTACT_EMAIL);
   }
}
