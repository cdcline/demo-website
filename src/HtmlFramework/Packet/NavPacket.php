<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use DB\PageNav;
use HtmlFramework\Packet\PacketTrait;
use Utils\StringUtils;
use Utils\SiteRunner;

class NavPacket {
   use PacketTrait;

   public function __construct(string $navText, array $pageNavs) {
      $this->setData('navText', $navText);
      $this->setData('navRows', $this->extractNavDataFromPageNavs($pageNavs));
   }

   private function extractNavDataFromPageNavs(array $pageNavs): array {
      // Filter to the page navs we care about
      $fPageNavs = array_filter($pageNavs, fn($pNav) => $pNav->displayInNav());
      // Turn the objects into arrays so we can do array magic
      $navRows = array_map(fn($pNav) => $pNav->toArray(), $fPageNavs);
      // We'll want to pull these values from the array
      $iNavPacket = ['url', 'theme', 'nav_string', 'orderby'];
      $navPacketData = StringUtils::array_column_multi($navRows, $iNavPacket);
      array_multisort(array_column($navPacketData, 'orderby'), SORT_ASC, $navPacketData);
      return $navPacketData;
   }
}
