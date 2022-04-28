<?php declare(strict_types=1);

namespace DB;

use DB\PDOConnection;
use Utils\Server as ServerUtils;

class PageIndex {
   public static function fetchAllRows(): array {
      if (!ServerUtils::onLiveSite()) {
         return self::staticPageIndexData();
      }
      return self::fetchAllPageIndexRows();
   }

   private static function fetchAllPageIndexRows(): array {
      $sql = <<<EOT
         SELECT `pageid`, `slug`, `nav_string`, `page_title`, `page_header`, `main_article`
         FROM `page_index`
         LEFT JOIN `page_nav` USING (`pageid`)
         ORDER BY `orderby` ASC
EOT;
      return PDOConnection::getConnection()->fetchAll($sql);
   }

   private static function staticPageIndexData(): array {
      return [
         ['pageid' => 1,
          'slug' => 'about-me',
          'nav_string' => 'About Me',
          'page_title' => 'About Me - Website Demo',
          'page_header' => 'About Me',
          'main_article' => <<<EOT
## This is the About Me Article!

I write code and don't have _any_ coding examples. I hope this will serve both as my personal website and an example of how I write code!
EOT
         ],
         ['pageid' => 2,
          'slug' => 'dev',
          'nav_string' => 'Dev',
          'page_title' => 'Dev - Website Demo',
          'page_header' => 'The Dev Environment',
          'main_article' => <<<EOT
## This is the Dev Article!

I need a space that's pretty constant and one that's _kinda_ scratch paper. This one's the scratch paper!
EOT
         ],
      ];
   }
}