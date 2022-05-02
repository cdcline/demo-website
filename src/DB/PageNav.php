<?php declare(strict_types=1);

namespace DB;

use DB\DBTrait;
use Utils\StringUtils;
use Pages\InvalidPageException;

class PageNav {
   use DBTrait;
   public const ARTICLE_PAGE_TYPE = 'ARTICLE_PAGE';
   public const CUSTOM_TYPE = 'CUSTOM';

   public static function getPageidFromSlug(string $slug): int {
      foreach (self::fetchAllRowsFromStaticCache() as $row) {
         if (StringUtils::iMatch($slug, $row['slug'])) {
            return (int)$row['pageid'];
         }
      }
      InvalidPageException::throwPageNotFound($slug);
   }

   private static function fetchAllRows(): array {
      $sql = <<<EOT
         SELECT `navid`, `type`, `slug`, `nav_string`, `pageid`, `orderby`
         FROM `page_nav`
         ORDER BY `orderby` ASC
EOT;
      return self::db()->fetchAll($sql);
   }

   private static function getStaticRows(): array {
      return [
         ['navid' => 1,
          'type' => self::ARTICLE_PAGE_TYPE,
          'slug' => 'about-me',
          'nav_string' => 'About Me',
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
