<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Packet\PacketTrait;
use Utils\HtmlUtils;

class HeaderPacket {
   public const FULL_WIDTH = 2000;
   public const FULL_HEIGHT = 600;
   public const MOBILE_WIDTH = 1200;
   public const MOBILE_HEIGHT = 1200;


   private $headerText;
   private $headerImages;

   use PacketTrait;

   public function __construct(string $headerText, array $headerImages) {
      $this->headerText = $headerText;
      $this->headerImages = $headerImages;
   }

   public function hasImages(): bool {
      return (bool)$this->getFullImages();
   }

   public function showCarousel(): bool {
      return count($this->headerImages['full']) > 1;
   }

   public function getHeaderText(): string {
      return $this->headerText;
   }

   public function getFullImages(): array {
      return $this->headerImages['full'];
   }

   public function getMobileImages(): array {
      return $this->headerImages['mobile'];
   }
}
