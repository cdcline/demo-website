<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Article as PageArticle;
use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Nav as PageNav;
use HtmlFramework\Packet\SectionPacket;

/**
 * The "section" houses the nav and acticle and allows the layout
 * to look good on smaller screen using css.
 */
class Section extends HtmlElement {
   private const FRAMEWORK_FILE = 'section.phtml';

   public static function fromValues(PageArticle $article, PageNav $nav): self {
      $packet = new SectionPacket($article, $nav);
      return new self($packet);
   }

   private function __construct(SectionPacket $packet) {
      $this->packet = $packet;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function printArticle(): void {
      $this->packet->printArticleHtml();
   }

   protected function printNav(): void {
      $this->packet->printNavHtml();
   }
}
