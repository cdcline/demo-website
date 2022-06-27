<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Packet\PacketTrait;

class HeaderPacket {
   private $headerText;
   private $headerImageSrc;

   use PacketTrait;

   public function __construct(string $headerText, ?string $headerImg) {
      $this->headerText = $headerText;
      $this->headerImageSrc = $headerImg;
   }

   public function getHeaderText(): string {
      return $this->headerText;
   }

   public function getPageIndexImage(): array {
      if (!$this->headerImageSrc) {
         return [];
      }

      return [
         'full' => $this->headerImageSrc,
         'mobile' => $this->hackMobleSrc($this->headerImageSrc)
      ];
   }

   private function hackMobleSrc(string $fullImageSrc) {
      $preg = '/(.+)(\.png)$/';
      return preg_replace($preg, '$1_mobile$2', $fullImageSrc);
   }
}
