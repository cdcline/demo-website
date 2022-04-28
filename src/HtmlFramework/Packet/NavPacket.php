<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use DB\PageNav;
use HtmlFramework\Packet\PacketTrait;
use Utils\StringUtils;

class NavPacket {
   use PacketTrait;

   public function __construct(array $pageNavRows) {
      return $this->setData('navRows', $this->extractNavDataFromPageNavRows($pageNavRows));
   }

   private function extractNavDataFromPageNavRows(array $pageNavRows): array {
      $navData = [];

      // Can be done in MySQL too but this helps with the static data
      array_multisort(array_column($pageNavRows, 'orderby'), SORT_ASC, $pageNavRows);

      // I want some elements to mess with on the site, we'll designate them "fun"
      $isFunRow = function($i): bool {
         // Every 3rd nav row is fun starting with the first
         return $i % 3 === 0;
      };

      foreach ($pageNavRows as $i => $row) {
         $navData[] = [
            'url' => $this->getUrlFromPageNavRow($row),
            'display' => $row['nav_string'],
            'isFun' => $isFunRow($i)
         ];
      }

      return $navData;
   }

   private function getUrlFromPageNavRow(array $navRow): string {
      $customUrl = StringUtils::iMatch($navRow['type'], PageNav::CUSTOM_TYPE);
      $slugOrUrl = $navRow['slug'];
      // If it's a "custom url" just use whatever is in the field
      return $customUrl ? $slugOrUrl : $this->generateUrlFromSlug($slugOrUrl);
   }

   // We'll assume all the "slug" urls are relative
   private function generateUrlFromSlug(string $slug) {
      return "/{$slug}";
   }
}
