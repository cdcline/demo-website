<?php declare(strict_types=1);

namespace DB;

use DB\PDOConnection;
use Utils\Server as ServerUtils;
use Utils\StringUtils;
use Pages\InvalidPageException;

class PageNav {
   /**
    * This is a poor way of caching but the data set should always be
    * pretty small and there's a future issue to actually do caching
    * correcty: https://github.com/cdcline/demo-website/issues/15
    */
   private static $staticRowCache;
   public const ARTICLE_PAGE_TYPE = 'ARTICLE_PAGE';
   public const CUSTOM_TYPE = 'CUSTOM';

   public static function fetchAllRows(): array {
      if (!ServerUtils::onLiveSite()) {
         return self::staticPageNavRows();
      }
      if (isset(self::$staticRowCache)) {
         return self::$staticRowCache;
      }
      return self::$staticRowCache = self::fetchAllPageNavRows();
   }

   public static function getPageidFromSlug(string $slug): int {
      foreach (self::fetchAllRows() as $row) {
         if (StringUtils::iMatch($slug, $row['slug'])) {
            return (int)$row['pageid'];
         }
      }
      throw new InvalidPageException($slug);
   }

   private static function fetchAllPageNavRows(): array {
      $sql = <<<EOT
         SELECT `navid`, `type`, `slug`, `nav_string`, `pageid`, `orderby`
         FROM `page_nav`
         ORDER BY `orderby` ASC
EOT;
      return PDOConnection::getConnection()->fetchAll($sql);
   }

   private static function staticPageNavRows(): array {
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
