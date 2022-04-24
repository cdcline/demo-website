<?php declare(strict_types=1);

namespace Utils;

use PDO;
use Utils\Server as ServerUtils;
use Utils\SecretManager;

class DB {
   private $pdo;

   public static function fetchPageIndexData(): array {
      if (!ServerUtils::onLiveSite()) {
         return self::staticPageIndexData();
      }

      $sql = <<<EOT
         SELECT `pageid`, `slug`, `nav_string`, `page_title`, `page_header`
         FROM `page_index`
         ORDER BY `pageid`
EOT;
      $db = new self();
      $pdoQuery = $db->query($sql);
      return $pdoQuery->fetchAll();
   }

   private function query(string $sqlQuery) {
      return $this->fetchPDO()->query($sqlQuery);
   }

   private function fetchPDO(): PDO {
      if ($this->pdo) {
         return $this->pdo;
      }

      $dbConnInfo = SecretManager::fetchDBConnectionInfo();
      $dbConn = $dbConnInfo['dbConn'];
      $dbName = $dbConnInfo['dbName'];
      $dbUser = $dbConnInfo['dbUser'];
      $dbPass = $dbConnInfo['dbPass'];
      $dsn = "mysql:unix_socket=/cloudsql/{$dbConn};dbname={$dbName}";
      return $this->pdo = new PDO($dsn, $dbUser, $dbPass);
   }

   private static function staticPageIndexData(): array {
      return [
         ['pageid' => 1,
          'slug' => 'about-me',
          'nav_string' => 'About Me',
          'page_title' => 'About Me - Website Demo',
          'page_header' => 'About Me'
         ],
         ['pageid' => 2,
          'slug' => 'dev',
          'nav_string' => 'Dev',
          'page_title' => 'Dev - Website Demo',
          'page_header' => 'The Dev Environment'
         ],
      ];
   }
}
