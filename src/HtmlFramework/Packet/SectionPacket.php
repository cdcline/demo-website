<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Nav as PageNav;
use HtmlFramework\Article as PageArticle;
use HtmlFramework\Packet\PacketTrait;

class SectionPacket {
   use PacketTrait;

   public function __construct(PageNav $nav, PageArticle $article) {
      $this->setData('nav', $nav);
      $this->setData('article', $article);
   }
}
