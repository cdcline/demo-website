<?php declare(strict_types=1);

namespace Utils;

use Parsedown;

class Parser {
   private static $parser;
   // Returns parsed text in block style
   public static function parseText(string $text): string {
      return self::getParser()->text($text);
   }

   // Returns parsed text in inline style
   public static function parseLine(string $text): string {
      return self::getParser()->line($text);
   }

   private static function getParser(): Parsedown {
      if (isset(self::$parser)) {
         return self::$parser;
      }
      $parser = new Parsedown();
      // Sanatizes the output (in theory)
      $parser->setSafeMode(true);
      return self::$parser = $parser;
   }
}
