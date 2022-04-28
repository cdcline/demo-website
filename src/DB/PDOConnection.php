<?php declare(strict_types=1);

namespace DB;

use PDO;
use PDOStatement;
use Utils\SecretManager;

class PDOConnection {
   private static $pdoConnection;
   private $pdo;
   private $secretInfo;
   /**
    * There isn't really a good reason to make a lot of PDOConnections so
    * we'll create one & just stick it in a static cache.
    *
    * Not the best behavior but it does the thing.
    */
   public static function getConnection(): PDOConnection {
      if (isset(self::$pdoConnection)) {
         return self::$pdoConnection;
      }
      return self::$pdoConnection = new PDOConnection();
   }

   public function fetchAll(string $sqlQuery): array {
      return $this->query($sqlQuery)->fetchAll();
   }

   private function query(string $sqlQuery): PDOStatement {
      return $this->getPDO()->query($sqlQuery);
   }

   private function getSecret(string $secretName): string {
      if (!isset($this->secretInfo)) {
         $this->secretInfo = SecretManager::fetchDBConnectionInfo();
      }
      return (string)$this->secretInfo[$secretName];
   }

   /**
    * Note: I think this can be done with runtime variables: https://github.com/cdcline/demo-website/issues/16
    * instead of secrets
    */
   private function generateSocketName(): string {
      $dbConn = $this->getSecret('dbConn');
      $dbName = $this->getSecret('dbName');
      return "mysql:unix_socket=/cloudsql/{$dbConn};dbname={$dbName}";
   }

   private function getPDO(): PDO {
      if (isset($this->pdo)) {
         return $this->pdo;
      }
      return $this->pdo = new PDO(
         $this->generateSocketName(),
         $this->getSecret('dbUser'),
         $this->getSecret('dbPass')
      );
   }
}
