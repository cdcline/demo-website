<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Packet\HeadPacket;
use Utils\HtmlUtils;

/**
 * The "head" element is a bit confusing because it's full of things that
 * the browser uses for all sorts of random stuff.
 *
 * It's not seen by the user and goes above the "body" element.
 */
class Head extends HtmlElement {
   private const FRAMEWORK_FILE = 'head.phtml';

   public static function fromValues(string $pageTitle): self {
      $packet = new HeadPacket($pageTitle);
      return new self($packet);
   }

   private function __construct(HeadPacket $packet) {
      $this->packet = $packet;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function getTitle(): string {
      return HtmlUtils::makeTitleElement($this->getPacketData('pageTitle'));
   }

   protected function getMeta(): string {
      $metaElData = [
         ['name' => 'robots', 'content' => 'noindex'],
         ['charset' => 'utf8'],
         ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']
      ];
      $metaEls = [];
      foreach ($metaElData as $mElData) {
         $metaEls[] = HtmlUtils::makeMetaElement($mElData);
      }
      return implode(' ', $metaEls);
   }

   protected function getScripts(): string {
      return HTMLUtils::makeScriptElement(['src' => $this->packet->getJavaScriptPath()]);
   }

   protected function getLinks(): string {
      return implode(' ', array_merge([$this->cssLink()], $this->favLinks()));
   }

   private function cssLink(): string {
      return HtmlUtils::makeLinkElement([
         'rel' => 'stylesheet',
         'href' => $this->packet->getStyleSheetPath()
      ]);
   }

   private function favLinks(): array {
      $fData = [
         ['rel' => 'icon', 'type' => 'image/x-icon', 'href' => $this->packet->getFavPath()],
         ['rel' => 'icon', 'type' => 'image/png', 'sizes'=>'16x16', 'href' => $this->packet->getFavPath('16')],
         ['rel' => 'icon', 'type' => 'image/png', 'sizes'=>'32x32', 'href' => $this->packet->getFavPath('32')],
         ['rel' => 'icon', 'type' => 'image/png', 'sizes'=>'192x192', 'href' => $this->packet->getFavPath('192')],
         ['rel' => 'icon', 'type' => 'image/png', 'sizes'=>'512x512', 'href' => $this->packet->getFavPath('512')],
         ['rel' => 'apple-touch-icon', 'href' => $this->packet->getFavPath('apple')],
         ['rel' => 'manifest', 'href' => $this->packet->getFavPath('manifest')]
      ];
      $favLinks = [];
      foreach ($fData as $data) {
         $favLinks[] = HtmlUtils::makeLinkElement($data);
      }
      return $favLinks;
   }
}
