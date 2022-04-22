<?php declare(strict_types=1);

namespace Utils;

class Server {
   public static function onLiveSite(): bool {
      return getenv('GOOGLE_CLOUD_PROJECT');
   }
}
