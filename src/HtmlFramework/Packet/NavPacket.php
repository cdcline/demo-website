<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use DB\PageNav;
use HtmlFramework\Packet\PacketTrait;
use Utils\StringUtils;
use Utils\SiteRunner;

class NavPacket {
   use PacketTrait;

   public function __construct(array $pageNavs) {
      $pageNavRows = array_map(fn($pNav) => $pNav->toArray(), $pageNavs);
      $this->setData('navRows', $this->extractNavDataFromPageNavRows($pageNavRows));
   }

   private function extractNavDataFromPageNavRows(array $pageNavRows): array {
      $navData = [];

      // Can be done in MySQL too but this helps with the static data
      array_multisort(array_column($pageNavRows, 'orderby'), SORT_ASC, $pageNavRows);

      $slug = SiteRunner::getSlugFromUrl();
      $isFunRow = function($rSlug) use ($slug): bool {
         return $rSlug === $slug;
      };

      foreach ($pageNavRows as $i => $row) {
         $navData[] = [
            'url' => $this->getUrlFromPageNavRow($row),
            'display' => $row['nav_string'],
            'theme' => $row['theme'],
            'isFun' => $isFunRow($row['slug'])
         ];
      }

      return $navData;
   }

   private function getUrlFromPageNavRow(array $navRow): string {
      // Don't have any url for the "homepage" link.
      if ($navRow['pageid'] === PageNav::DEFAULT_PAGEID) {
         return '/';
      }
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
