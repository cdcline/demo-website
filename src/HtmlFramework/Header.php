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
    *      <div class="header-slideshow-container">
    *         <div class="header-slideshow-images slider-container-transition full-header-images">
    *            <img class="header-image full-size" src="<?= $headerImgSrc ?>" />
    *            { ... }
    *         </div>
    *         <div class="header-slideshow-images slider-container-transition mobile-header-images">
    *            <img class="header-image mobile-size" src="<?= $headerImgSrc ?>" />
    *            { ... }
    *         </div>
    *      </div>
    *      <h2><?= $this->getHeaderText ?></h2>
    *    </div>
    */
   protected function getHeaderContentHtml(): string {
      $containerClasses = ['header-main-title-container'];
      $headerContentEls = [];
      $headerCarousel = $this->packet->getSlideshows();
      if ($headerCarousel) {
         $containerClasses[] = 'image-header-container';
         $fullSlideshow = $this->buildSlideshowHtml($headerCarousel['full'], /*full*/true);
         $mobileSlideshow = $this->buildSlideshowHtml($headerCarousel['mobile'], /*full*/false);

         $carouselEls = [
           $fullSlideshow,
           $mobileSlideshow,
           HtmlUtils::makeDivElement('Next', ['class' => 'js-next-button'])
         ];
         $headerContentEls[] = HtmlUtils::makeDivElement(implode(' ', $carouselEls), ['class' => 'header-slideshow-container']);
      }
      $headerContentEls[] = HtmlUtils::makeHXElement(2, $this->packet->getHeaderText());
      // Build the Container
      $contentHtml = implode (' ', $headerContentEls);
      $contentParts = ['class' => implode(' ', $containerClasses)];
      return HtmlUtils::makeDivElement($contentHtml, $contentParts);
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   private function buildSlideshowHtml(array $images, bool $isFullImage): string {
      $addClass = $isFullImage ? 'full-header-imaages' : 'mobile-header-images';
      $slideshowClasses = ['js-flex-carousel', 'header-slideshow-images', $addClass];
      $slideshowParams = ['class' => implode(' ', $slideshowClasses)];
      $imageEls = [];
      $order = 0;
      foreach ($images as $iData) {
         ++$order;
         $imageEls[] = $this->buildImageHtml($isFullImage, $iData['src'], $order);
      }
      return HtmlUtils::makeDivElement(implode(' ', $imageEls), $slideshowParams);
   }


   private function buildImageHtml(bool $isFullImg, string $imageSrc, int $position) {
      $index = $isFullImg ? 'full' : 'mobile';
      $typeClass = $isFullImg ? 'page-background' : 'mobile-background';
      $imageClasses = "js-carousel-slide header-image {$typeClass}";
      $width = $isFullImg ? HeaderPacket::FULL_WIDTH : HeaderPacket::MOBILE_WIDTH;
      $height = $isFullImg ? HeaderPacket::FULL_HEIGHT: HeaderPacket::MOBILE_HEIGHT;
      return HtmlUtils::makeImageElement(['src' => $imageSrc, 'class' => $imageClasses, 'width' => $width, 'height' => $height, 'data-position' => $position]);
   }

   private function __construct(HeaderPacket $packet) {
      $this->packet = $packet;
   }
}
