<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use DB\PageNav;
use HtmlFramework\Packet\PacketTrait;
use Utils\StringUtils;

class FooterPacket {
   private $footerRows;
   private $navText;

   use PacketTrait;

   public function __construct(string $navText) {
      $this->navText = $navText;
      $this->footerRows = $this->extractNavDataFromPageNavs();
   }

   private function extractNavDataFromPageNavs(): array {
      $pageNavs = PageNav::fetchAllRowsFromStaticCache();
      // Filter to the page navs we care about
      $fPageNavs = array_filter($pageNavs, fn($pNav) => $pNav->displayInFooter());
      // Turn the objects into arrays so we can do array magic
      $footerRows = array_map(fn($pNav) => $pNav->toArray(), $fPageNavs);
      // We'll want to pull these values from the array
      $iFooterPacket = ['url', 'nav_string', 'img_src', 'orderby'];
      $footerPacketData = StringUtils::array_column_multi($footerRows, $iFooterPacket);
      array_multisort(array_column($footerPacketData, 'orderby'), SORT_ASC, $footerPacketData);
      return $footerPacketData;
   }

   public function getNavText(): string {
      return $this->navText ?: 'You might also enjoy checking out these other pages';
   }

   public function getFooterRows(): array {
      return $this->footerRows;
   }
}
