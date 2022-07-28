<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use DB\PageIndex;
use HtmlFramework\Packet\PacketTrait;

class HeaderPacket {
   public const FULL_WIDTH = 2000;
   public const FULL_HEIGHT = 600;
   public const MOBILE_WIDTH = 1200;
   public const MOBILE_HEIGHT = 1200;

   private $pageType;
   private $headerText;
   private $headerImages;

   use PacketTrait;

   public function __construct(string $pageType, string $headerText, array $headerImages) {
      $this->pageType = $pageType;
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

   public function getHeaderTemplate(): string {
      switch ($this->pageType) {
         case PageIndex::HOMEPAGE_TYPE: return 'src/templates/welcome_header.phtml';
         case PageIndex::ROBOTS_TYPE: return 'src/templates/robots_header.phtml';
      }
      return '';
   }
}
