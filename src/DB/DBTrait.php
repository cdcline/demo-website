<?php declare(strict_types=1);

namespace DB;

use Utils\FirestoreConverter;
use Utils\ServerUtils;

/**
 * This is a very basic trait that does a couple simple things
 *  - Creates a "Static Cache"
 *    - Allows us to query data from a source (MySQL or on file) then stick it
 *      in the running processes memory so if we ask for the data again, we have
 *      the values in the running process and don't have to go to the source again.
 */
trait DBTrait {
   private static $db;
   private static $staticRowCache;

   /**
    * This should return a set of values matching the structure we'd get out of `fetchAllRows`.
    * Used for Dev when we don't want to worry about fetching data from elsewhere.
    */
   // Boo, App deploys on 7.3 and we can't do this. Pretend like this is here.
   // abstract private static function getHardcodedRows();

   /**
    * The actual query we'd like to stick in the "static cache"
    */
   // Boo, App deploys on 7.3 and we can't do this. Pretend like this is here.
   // abstract private static function fetchAllRows();

   /**
    * This is a poor way of caching but the data set should always be
    * pretty small and there's a future issue to actually do caching
    * correcty: https://github.com/cdcline/demo-website/issues/15
    */
   public static function fetchAllRowsFromStaticCache(): array {
      if ($sCache = self::getStaticCache()) {
         return $sCache;
      }
      $rows = ServerUtils::useBackendDB() ? self::fetchAllRows() : self::getHardcodedRows();
      return self::setStaticCache($rows);
   }

   protected static function setStaticCache(array $rows): array {
      return self::$staticRowCache = $rows;
   }

   protected static function getStaticCache(): ?array {
      return isset(self::$staticRowCache) ? self::$staticRowCache : null;
   }

   private static function fetchRows(string $path, array $docVaules, array $snapValues = [], $convertFunc = null): array {
      return FirestoreConverter::fromValues($path, $docVaules, $snapValues, $convertFunc);
   }
}
