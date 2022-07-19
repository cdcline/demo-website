<?php declare(strict_types=1);

namespace Utils;

/**
 * A general file to keep server configuration logic.
 *
 * Should be mostly static functions based on environment variables, backend
 * or server values.
 */
class ServerUtils {
   public static function useBackendDB(): bool {
      return false; //self::onGoogleCloudProject();
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

   public static function getHostedImagePath(): string {
      $siteName = getenv('GOOGLE_CLOUD_PROJECT');
      return "https://storage.googleapis.com/{$siteName}.appspot.com/images/site/";
   }
}
