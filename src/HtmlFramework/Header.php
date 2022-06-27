<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Packet\HeaderPacket;
use Utils\HtmlUtils;

/**
 * Each page has a large header with text; this is the element
 * that prints it out.
 *
 * The actual text is created in the Page objects.
 */
class Header extends HtmlElement {
   private const FRAMEWORK_FILE = 'header.phtml';

   public static function fromValues(string $headerText, ?string $headerImg): self {
      $packet = new HeaderPacket($headerText, $headerImg);
      return new self($packet);
   }

   /**
    *    <div class="header-main-title-container {if $headerImage)image-header-container{endif}">
    *      <img class="image-header page-background" src="<?= $headerImgSrc ?>" />
    *      <img class="image-header mobile-background" src="<?= $mobileSrc ?>" />
    *      <h2><?= $this->getHeaderText ?></h2>
    *    </div>
    */
   protected function getHeaderContentHtml(): string {
      $containerClasses = ['header-main-title-container'];
      // Build the Text Header
      $headerContentEls = [HtmlUtils::makeHXElement(2, $this->packet->getHeaderText())];
      // Build the Images
      $imgClasses = ['image-header'];
      $headerImages = $this->packet->getPageIndexImage();
      if ($headerImages) {
         $containerClasses[] = 'image-header-container';
         $fullImageClasses = implode(' ', array_merge($imgClasses, ['page-background']));
         $mobileImageClasses = implode(' ', array_merge($imgClasses, ['mobile-background']));
         $fullImageHtml = HtmlUtils::makeImageElement(['src' => $headerImages['full'], 'class' => $fullImageClasses]);
         $mobileImageHtml = HtmlUtils::makeImageElement(['src' => $headerImages['mobile'], 'class' => $mobileImageClasses]);
         $headerContentEls = array_merge([$fullImageHtml, $mobileImageHtml], $headerContentEls);
      }
      // Build the Container
      $contentHtml = implode (' ', $headerContentEls);
      $contentParts = ['class' => implode(' ', $containerClasses)];
      return HtmlUtils::makeDivElement($contentHtml, $contentParts);
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   private function __construct(HeaderPacket $packet) {
      $this->packet = $packet;
   }
}
