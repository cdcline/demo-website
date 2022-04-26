<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Header as PageHeader;
use HtmlFramework\Section as PageSection;
use HtmlFramework\Footer as PageFooter;
use HtmlFramework\Packet\PacketTrait;

class BodyPacket {
   use PacketTrait;

   public function __construct(PageHeader $header, PageSection $section, PageFooter $footer) {
      $this->setData('header', $header);
      $this->setData('section', $section);
      $this->setData('footer', $footer);
   }
}
