<?php declare(strict_types=1);

namespace HtmlFramework;

use DB\PageNav;
use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Packet\NavPacket;
use Utils\HtmlUtils;

/**
 * The "nav" element has a bunch of links to urls & lives in the
 * "body" element.
 */
class Nav extends HtmlElement {
   private const FRAMEWORK_FILE = 'nav.phtml';

   public static function fromValues(bool $hideMainNav): self {
      $navPacket = new NavPacket(PageNav::fetchAllRowsFromStaticCache(), $hideMainNav);
      return new self($navPacket);
   }

   private function __construct(NavPacket $packet) {
      $this->packet = $packet;
   }

   /**
    * <div class="nav-text-container">
    *    <h3>$text</h3>
    * </div>
    */

   protected function getNavTextHtml(): string {
      $navText = $this->packet->getData('navText');
      if (!$navText) {
         return '';
      }
      return HtmlUtils::makeDivElement(
         HtmlUtils::makeHXElement(3, $this->packet->getData('navText')),
         ['class' => 'nav-text-container']
      );
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }
}
