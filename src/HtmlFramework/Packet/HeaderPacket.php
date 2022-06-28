<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Packet\PacketTrait;
use Utils\HtmlUtils;

class HeaderPacket {
   public const FULL_WIDTH = 2000;
   public const FULL_HEIGHT = 600;
   public const MOBILE_WIDTH = 1200;
   public const MOBILE_HEIGHT = 1200;

   private const RANDOM_MARGIN = 10;

   private $headerText;
   private $headerImageSrc;
   private $images;

   use PacketTrait;

   public function __construct(string $headerText, ?string $headerImg) {
      $this->headerText = $headerText;
      $this->headerImageSrc = $headerImg;
   }

   public function getHeaderText(): string {
      return $this->headerText;
   }

   public function getSlideshows(): array {
      if (isset($this->slideshows)) {
         return $this->slideshows;
      }

      $addPicsumImages = $this->getNumTestImages();

      do {
         $mobileImageSrc[] = $this->getRandomMobileHeaderImgSrc();
         $fullImageSrc[] = $this->getRandomFullHeaderImgSrc();
      } while ($addPicsumImages--);

      if ($this->headerImageSrc) {
         $mobileImageSrc = array_merge([$this->hackMobleSrc($this->headerImageSrc)], $mobileImageSrc);
         $fullImageSrc = array_merge([$this->headerImageSrc], $fullImageSrc);
      }


      return $this->slideshows = [
         'mobile' => $this->buildImageData($mobileImageSrc),
         'full' => $this->buildImageData($fullImageSrc)
      ];
   }

   private function buildImageData(array $imageSrc): array {
      $imageData = [];
      foreach ($imageSrc as $src) {
         $imageData[] = [
            'src' => $src,
         ];
      }
      return $imageData;
   }

   private function getRandomFullHeaderImgSrc(): string {
      $width = rand(self::FULL_WIDTH - self::RANDOM_MARGIN, self::FULL_WIDTH + self::RANDOM_MARGIN);
      $height = rand(self::FULL_HEIGHT - self::RANDOM_MARGIN, self::FULL_HEIGHT + self::RANDOM_MARGIN);
      return HtmlUtils::getPicsumPhoto($width, $height);
   }

   private function getRandomMobileHeaderImgSrc(): string {
      $width = rand(self::MOBILE_WIDTH - self::RANDOM_MARGIN, self::MOBILE_WIDTH + self::RANDOM_MARGIN);
      $height = rand(self::MOBILE_HEIGHT - self::RANDOM_MARGIN, self::MOBILE_HEIGHT + self::RANDOM_MARGIN);
      return HtmlUtils::getPicsumPhoto($width, $height);
   }

   // For now we're just gonna add some randome images to the Life page
   private function getNumTestImages(): int {
      return $this->headerText === 'Life' ? 4 : 0;
   }

   private function hackMobleSrc(string $fullImageSrc) {
      $preg = '/(.+)(\.png)$/';
      return preg_replace($preg, '$1_mobile$2', $fullImageSrc);
   }
}
