<?php declare(strict_types=1);

namespace DB;

use PDO;
use PDOStatement;
use Utils\SecretManager;

class PDOConnection {
   private $secretInfo;
   /**
    * It shouldn't matter much if we make a few connections so for now we'll
    * just make a new PDO for every query.
    */
   public static function getConnection(): PDOConnection {
      return new self();
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
      $pdo = new PDO(
         $this->generateSocketName(),
         $this->getSecret('dbUser'),
         $this->getSecret('dbPass')
      );
      // Throw errors instead of returning false
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $pdo;
   }
}
