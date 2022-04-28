<?php declare(strict_types=1);

namespace Utils;

use strcasecmp;

class StringUtils {
   /**
    * Insensitive string match. Here b/c the strcasecmp logic is silly.
    */
   public static function iMatch(string $str1, string $str2): bool {
      return strcasecmp($str1, $str2) === 0;
   }
}
