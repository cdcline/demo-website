<?php declare(strict_types=1);

namespace DB;

use DB\DBTrait;
use Exception;
use Pages\InvalidPageException;
use Utils\StringUtils;

class PageNav {
   use DBTrait;

   public const ARTICLE_PAGE_TYPE = 'ARTICLE_PAGE';
   public const CUSTOM_TYPE = 'CUSTOM';
   public const HOMEPAGE_PAGEID = 1;

   public static function getPageidFromSlug(string $slug = ''): int {
      if (!$slug) {
         return self::HOMEPAGE_PAGEID;
      }
      foreach (self::testFirestore() as $row) {
         if (StringUtils::iMatch($slug, $row['slug'])) {
            return (int)$row['pageid'];
         }
      }
      InvalidPageException::throwPageNotFound($slug);
   }

   public static function getDefaultSlug(): string {
      $dPageid = self::getPageidFromSlug();
      foreach (self::testFirestore() as $row) {
         if ($row['pageid'] === $dPageid) {
            return $row['slug'];
         }
      }
      throw new Exception('Default page not configured correctly. Unkown HOMEPAGE_PAGEID.');
   }

   private static function fetchAllRows(): array {
      $fParams = [
         'strIndexes' => ['type', 'slug', 'nav_string', 'orderby'],
         'snapIndexes' => [['strIndex' => 'page', 'snapIndex' => 'pageid', 'newIndex' => 'pageid']]
      ];
      return self::fetchFireRows('page_nav', $fParams);
   }

   // NOTE: Order of the data matters, should match `fetchAllRows`
   private static function getHardcodedRows(): array {
      return [
         ['navid' => 1,
          'type' => self::ARTICLE_PAGE_TYPE,
          'slug' => 'homepage',
          'nav_string' => 'Homepage',
          'pageid' => 1,
          'orderby' => 1
         ],
         ['navid' => 2,
          'type' => self::CUSTOM_TYPE,
          'slug' => 'https://github.com/cdcline/demo-website',
          'nav_string' => 'Resume',
          'pageid' => NULL,
          'orderby' => 3
         ],
         ['navid' => 3,
          'type' => self::ARTICLE_PAGE_TYPE,
          'slug' => 'dev',
          'nav_string' => 'Dev',
          'pageid' => 2,
          'orderby' => 2
         ]
      ];
   }
}
