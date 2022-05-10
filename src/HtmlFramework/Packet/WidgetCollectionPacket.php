<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Packet\PacketTrait;

class WidgetCollectionPacket {
   use PacketTrait;

   private $pageType;
   private $pageid;

   public static function fromValues(string $pageType, int $pageid): self {
      return new self($pageType, $pageid);
   }

   public function getPageType(): string {
      return $this->pageType;
   }

   public function getPageid(): int {
      return $this->pageid;
   }

   private function __construct(string $pageType, int $pageid) {
      $this->pageType = $pageType;
      $this->pageid = $pageid;
   }
}
