<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Article as PageArticle;
use HtmlFramework\Nav as PageNav;
use HtmlFramework\Packet\PacketTrait;

class SectionPacket {
   use PacketTrait;

   private $pageArticle;
   private $pageNav;

   public function __construct(PageArticle $article, PageNav $nav) {
      $this->pageArticle = $article;
      $this->pageNav = $nav;
   }

   public function printArticleHtml(): void {
      $this->pageArticle->printHtml();
   }

   public function printNavHtml(): void {
      $this->pageNav->printHtml();
   }
}
