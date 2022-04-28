<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Packet\PacketTrait;

class NavPacket {
   use PacketTrait;

   public function __construct(array $pageIndexRows) {
      return $this->setData('navIndexRows', $this->extractNavDataFromPageIndexRows($pageIndexRows));
   }

   private function extractNavDataFromPageIndexRows(array $pageIndexRows): array {
      $navData = [];
      foreach ($pageIndexRows as $i => $row) {
         $navData[] = [
            'url' => "/{$row['slug']}",
            'display' => $row['nav_string'],
            'isFun' => $i % 3 === 0 // Every 3rd is fun starting with the first
         ];
      }
      return $navData;
   }
}
