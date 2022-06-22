<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Packet\PacketTrait;
use Utils\StringUtils;

class NavPacket {
   use PacketTrait;

   private $navRows;
   private $hideMainNav;

   public function __construct(array $pageNavs, bool $hideMainNav) {
      $this->hideMainNav = $hideMainNav;
      $this->navRows = $this->extractNavDataFromPageNavs($pageNavs);
   }

   public function showMainNav(): bool {
      return !$this->hideMainNav;
   }

   public function getNavSections(): array {
      // Hack b/c I don't want to add ordering to sections but I care about the order
      $sections = ['About' => [], 'Contact' => [], 'Code Features' => []];
      foreach ($this->navRows as $navRow) {
         $navSection = $navRow['section'] ?? 'UNKNOWN';
         $isNewSection = !isset($sections[$navSection]) || !isset($sections[$navSection]['title']);
         if ($isNewSection) {
            $sections[$navSection] = [
               'title' => $navSection,
               'entries' => []
            ];
         }
         $sections[$navSection]['entries'][] = $navRow;
      }
      return $sections;
   }

   private function extractNavDataFromPageNavs(array $pageNavs): array {
      // Filter to the page navs we care about
      $fPageNavs = array_filter($pageNavs, fn($pNav) => $pNav->displayInNav());
      // Turn the objects into arrays so we can do array magic
      $navRows = array_map(fn($pNav) => $pNav->toArray(), $fPageNavs);
      // We'll want to pull these values from the array
      $iNavPacket = ['section', 'url', 'is_image', 'img_src', 'is_viewed', 'nav_string', 'orderby'];
      $navPacketData = StringUtils::array_column_multi($navRows, $iNavPacket);
      array_multisort(array_column($navPacketData, 'orderby'), SORT_ASC, $navPacketData);
      return $navPacketData;
   }
}
