<?php declare(strict_types=1);

namespace DB;

//use Utils\FirestoreConverter;
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

   abstract private static function getDevStaticData(): array;
   abstract private static function getLiveStaticData(): array;

   /**
    * This is a poor way of caching but the data set should always be
    * pretty small and there's a future issue to actually do caching
    * correcty: https://github.com/cdcline/demo-website/issues/15
    */
   public static function fetchAllRowsFromStaticCache(): array {
      if ($sCache = self::getStaticCache()) {
         return $sCache;
      }
      $rows = ServerUtils::useBackendDB() ? self::fetchAllRows() : self::getStaticData();
      return self::setStaticCache($rows);
   }

   protected static function setStaticCache(array $rows): array {
      return self::$staticRowCache = $rows;
   }

   protected static function getStaticCache(): ?array {
      return isset(self::$staticRowCache) ? self::$staticRowCache : null;
   }

   /**
    * The actual firestore query we'd like to stick in the "static cache"
    */
   private static function fetchRows(string $path, array $docVaules, array $snapValues = [], $convertFunc = null): array {
      return [];//FirestoreConverter::fromValues($path, $docVaules, $snapValues, $convertFunc);
   }

   /**
    * This should return a set of values matching the structure we'd get out of `Firestore`.
    *
    * Used for Dev when we don't want to worry about fetching data from elsewhere.
    * Also used on the live site b/c Firestore is broken
    */
   private static function getStaticData(): array {
      if (ServerUtils::onGoogleCloudProject()) {
         return self::getLiveStaticData();
      } else {
         return self::getDevStaticData();
      }
   }
}
