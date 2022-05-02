<?php declare(strict_types=1);

namespace DB;

use DB\PDOConnection;
use Utils\ServerUtils;

trait DBTrait {
   private static $staticRowCache;

   abstract private static function getStaticRows();
   abstract private static function fetchAllRows();

   public static function fetchAllRowsFromStaticCache(): array {
      if ($sCache = self::getStaticCache()) {
         return $sCache;
      }
      $rows = ServerUtils::useBackendDB() ? self::fetchAllRows() : self::getStaticRows();
      return self::setStaticCache($rows);
   }

   private static function db(): PDOConnection {
      return PDOConnection::getConnection();
   }

   /**
    * This is a poor way of caching but the data set should always be
    * pretty small and there's a future issue to actually do caching
    * correcty: https://github.com/cdcline/demo-website/issues/15
    */
   private static function setStaticCache(array $rows): array {
      return self::$staticRowCache = $rows;
   }

   private static function getStaticCache(): ?array {
      return isset(self::$staticRowCache) ? self::$staticRowCache : null;
   }
}
