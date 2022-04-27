<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Footer as PageFooter;
use HtmlFramework\Header as PageHeader;
use HtmlFramework\Packet\BodyPacket;
use HtmlFramework\Section as PageSection;

/**
 * The "Body" is the section of html that holds most of the stuff the user will
 * see.
 *
 * It's basically everything but the "head" element used by browsers for browser
 * things.
 */

class Body extends HtmlElement {
   private const FRAMEWORK_FILE = 'body.phtml';

   public static function fromValues(PageHeader $header, PageSection $section, PageFooter $footer): self {
      $packet = new BodyPacket($header, $section, $footer);
      return new self($packet);
   }

   private function __construct(BodyPacket $packet) {
      $this->packet = $packet;
   }

   protected function printHeader(): void {
      $this->getPacketData('header')->printHtml();
   }

   protected function printSection(): void {
      $this->getPacketData('section')->printHtml();
   }

   protected function printFooter(): void {
      $this->getPacketData('footer')->printHtml();
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function getJavascriptPath(): string {
      return 'src/templates/js/page.js';
   }
}
