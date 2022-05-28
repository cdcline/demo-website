<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Footer as PageFooter;
use HtmlFramework\Nav as PageNav;
use HtmlFramework\Article as PageArticle;
use HtmlFramework\Packet\SectionPacket;

/**
 * The "section" houses the nav and acticle and allows the layout
 * to look good on smaller screen using css.
 */
class Section extends HtmlElement {
   private const FRAMEWORK_FILE = 'section.phtml';

   public static function fromValues(PageArticle $article, PageNav $nav,  PageFooter $footer): self {
      $packet = new SectionPacket($article, $nav, $footer);
      return new self($packet);
   }

   private function __construct(SectionPacket $packet) {
      $this->packet = $packet;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }
}
