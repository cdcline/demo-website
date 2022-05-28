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

   /**
    * For an array of values, returns an array of all the column keys for each
    * element in the array. Helpful to limit scope of values being passed around.
    *
    * $a = [
    *   ['foo' => 'bar1', 'fizz' => 'buzz1', 'blah' => 1, 'blahblah' => 2],
    *   ['foo' => 'bar2', 'fizz' => 'buzz2', 'blah' => 1, 'blahblah' => 2],
    * ];
    * $result = array_column_multi($a, ['foo', 'fizz'])
    * $result => [
    *   ['foo' => 'bar1', 'fizz' => 'buzz1'],
    *   ['foo' => 'bar2', 'fizz' => 'buzz2'],
    * ];
    *
    * Thx: https://www.php.net/manual/en/function.array-column.php#118763
    */
   public static function array_column_multi(array $input, array $column_keys): array {
      $result = [];
      $column_keys = array_flip($column_keys);
      foreach($input as $key => $el) {
          $result[$key] = array_intersect_key($el, $column_keys);
      }
      return $result;
   }
}
