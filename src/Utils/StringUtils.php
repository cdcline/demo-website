<?php declare(strict_types=1);

namespace Utils;

class StringUtils {
   /**
    * Insensitive string match. Here b/c the strcasecmp logic is silly.
    */
   public static function iMatch(string $str1, string $str2): bool {
      return strcasecmp($str1, $str2) === 0;
   }

   // Thanks! https://stackoverflow.com/a/55812086
   public static function filterArrayByKeys(array $input, array $column_keys) {
      $result = [];
      $column_keys = array_flip($column_keys); // getting keys as values
      foreach ($input as $key => $val) {
            // getting only those key value pairs, which matches $column_keys
         $result[$key] = array_intersect_key($val, $column_keys);
      }
      return $result;
   }

   public static function isInt(string $str): bool {
      return ctype_digit($str) && is_numeric($str);
   }
}
