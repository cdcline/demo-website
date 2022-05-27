<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Article as PageArticle;
use HtmlFramework\Footer as PageFooter;
use HtmlFramework\Nav as PageNav;
use HtmlFramework\Packet\PacketTrait;

class SectionPacket {
   use PacketTrait;

   public function __construct(PageArticle $article, PageNav $nav, PageFooter $footer) {
      $this->setData('article', $article);
      $this->setData('nav', $nav);
      $this->setData('footer', $footer);
   }
}
