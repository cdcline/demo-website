<?php declare(strict_types=1);

namespace Utils;

/**
 * A general file to keep server configuration logic.
 *
 * Should be mostly static functions based on environment variables, backend
 * or server values.
 */
class ServerUtils {
   // DBs are expensive; we'll use static data when we can: https://github.com/cdcline/demo-website/issues/50
   public static function useBackendDB(): bool {
      // Each db fetchAll call should have some static data representing it.
      // This is the switch to flip for static (hardcoded) data vs actual backend calls.
      // Can easily flip on or off before deploy if we just need to test the site display logic.
      return false;
      // What we would prefer to do after https://github.com/cdcline/demo-website/issues/51
      // > return self::onGoogleCloudProject():
   }

   // We want to load the google tools if we're on the google cloud.
   // We don't want to load them locally b/c of the added dev complexity
   public static function shouldLoadGoogleTools(): bool {
      return self::onGoogleCloudProject();
   }

   // Don't try to load Google Secrets if we are't in the Google Cloud
   public static function shouldUseGoogleSecrets(): bool {
      return self::onGoogleCloudProject();
   }

   // Google Cloud will set this value. We can look if it exists for
   // an ez "are we in the cloud" check.
   public static function onGoogleCloudProject(): bool {
      return (bool)getenv('GOOGLE_CLOUD_PROJECT');
   }
}
