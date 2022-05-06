<?php declare(strict_types=1);

namespace DB;

use DB\DBTrait;
use Utils\StringUtils;
use Pages\InvalidPageException;

class PageNav {
   use DBTrait;

   public const ARTICLE_PAGE_TYPE = 'ARTICLE_PAGE';
   public const CUSTOM_TYPE = 'CUSTOM';
   public const HOMEPAGE_PAGEID = 1;

   public static function getPageidFromSlug(string $slug = ''): int {
      if (!$slug) {
         return self::HOMEPAGE_PAGEID;
      }
      foreach (self::fetchAllRowsFromStaticCache() as $row) {
         if (StringUtils::iMatch($slug, $row['slug'])) {
            return (int)$row['pageid'];
         }
      }
      InvalidPageException::throwPageNotFound($slug);
   }

   public static function getDefaultSlug(): string {
      $dPageid = self::getPageidFromSlug();
      foreach (self::fetchAllRowsFromStaticCache() as $row) {
         if ($row['pageid'] === $dPageid) {
            return $row['slug'];
         }
      }
      throw new Exception('Default page not configured correctly. Unkown HOMEPAGE_PAGEID.');
   }

   private static function fetchAllRows(): array {
      $sql = <<<EOT
         SELECT `navid`, `type`, `slug`, `nav_string`, `pageid`, `orderby`
         FROM `page_nav`
         ORDER BY `orderby` ASC
EOT;
      return self::db()->fetchAll($sql);
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
