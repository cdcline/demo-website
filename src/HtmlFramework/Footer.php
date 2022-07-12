<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Packet\FooterPacket;
use Utils\HtmlUtils;

class Footer extends HtmlElement {
   private const FRAMEWORK_FILE = 'footer.phtml';

   public static function fromValues(string $navText): self {
      $packet = new FooterPacket($navText);
      return new self($packet);
   }

   private function __construct(FooterPacket $packet) {
      $this->packet = $packet;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   /**
    * <div class="page-footer-container">
    *    <div class="page-footer">
    *       <div class="page-footer-entry-container page-footer-text-entry">
    *           <h3>{$navText}</h3>
    *       </div>
    *       ...foreach entry ...
    *       <div class="page-footer-entry-container">
    *           <a href="{$url}">
    *              <div class="page-footer-entry-entery>
    *                   <img />
    *                   <span>{$navString></span>
    *              </div>
    *           </a>
    *       </div>
    *    </div>
    * <div>
    */
   public function printPageFooter(): void {
      $navTextHtml = HtmlUtils::makeH3Element($this->packet->getNavText());
      $entryContainerClass = 'page-footer-entry-container';
      $navTextEntry = HtmlUtils::makeDivElement($navTextHtml, ['class' => "$entryContainerClass page-footer-text-entry"]);


      $linkEntries = [];
      foreach ($this->packet->getFooterRows() as $fRow) {
         $imgSrc = $fRow['img_src'] ?: HtmlUtils::getPicsumPhoto(200, 200);
         $imgHtml = HtmlUtils::makeImageElement(['src' => $imgSrc]);
         $txtSpan = HtmlUtils::makeSpanElement($fRow['nav_string'], []);
         $aHtml = HtmlUtils::makeWebLinkElement($fRow['url'], $txtSpan . $imgHtml);
         $linkEntries[] = HtmlUtils::makeDivElement($aHtml, ['class' => $entryContainerClass]);
      }

      $pageFooterHtml = HtmlUtils::makeDivElement($navTextEntry . implode($linkEntries), ['class' => 'page-footer']);
      echo HtmlUtils::makeDivElement($pageFooterHtml, ['class' => 'page-footer-container']);
   }

   public function printMobileFooter(): void {
      $this->printHtml();
   }
}
