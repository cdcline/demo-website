<?php declare(strict_types=1);

namespace Utils;

class HtmlUtils {
   /**
    * NOTE: These element functions are silly, they're mostly just examples of
   * what you can do. There are better libraries that handle much more complicated
   * cases.
   */
   public static function makeImageElement(string $class, string $src, int $width, int $height, string $alt, string $id = ''): string {
      $elementParts = [
         '<img',
         "class=\"{$class}\"",
         "src=\"{$src}\"",
         "width=\"{$width}\"",
         "height=\"{$height}\"",
         "alt=\"{$alt}\""
      ];
      if ($id) {
         $elementParts[] = "id=\"$id\"";
      }
      $elementParts[] = ">";
      return implode(' ', $elementParts);
   }

   public static function makeSpanElement(string $text, string $class): string {
      return "<span class=\"{$class}\">{$text}</span>";
   }

   public static function makeH1Element(string $text, string $class): string {
      return "<h1 class=\"{$class}\">{$text}</h1>";
   }

   public static function makePageWhitespace(): string {
      return '<br /><br />';
   }

   public static function makeTableElement(array $tableData) {
      $caption = isset($tableData['caption']) ? $tableData['caption'] : '';
      $tableRows = $tableData['rows'];
      $headerRows = $tableData['header'];

      $generateRow = function(array $rowData, bool $isHead = false): string {
         $tCols = [];
         foreach ($rowData as $data) {
            $tCols[] = $isHead ? "<th>{$data}</th>" : "<td>{$data}</td>";
         }
         $tRowData = implode(' ', $tCols);
         return "<tr>{$tRowData}</tr>";
      };

      $headerRow = $generateRow($headerRows, /*isHead*/true);
      $tRows = [$headerRow];
      foreach($tableRows as $tableRow) {
         $tRows[] = $generateRow($tableRow);
      }
      $tRowsStr = implode(' ', $tRows);
      $captionStr = $caption ? "<caption>{$caption}</caption>" : '';
      return "<table>{$captionStr}{$tRowsStr}</table>";
   }

   // Will generate a span with the class "fun"
   public static function makeFunSpan(string $text): string {
      return self::makeSpanElement($text, 'fun');
   }

   // A random public image library
   public static function getPicsumPhoto(int $width, int $height): string {
      return "https://picsum.photos/{$width}/{$height}";
   }

   /**
    * Silly function to generate a list of space separated spans with the fun class.
    *
    * <span>text1</span> <span>text2</span> <span>text3</span>
    **/
   public static function makeFunSpanFromArray(array $textValues) {
      $fun = [];
      foreach ($textValues as $text) {
         $fun[] = self::makeFunSpan($text);
      }
      return implode(' ', $fun);
   }

   public static function addRandomFun(string $text, int $randomizeAmount): string {
      $words = explode(' ', $text);
      $newText = [];
      $isFun = function() use ($randomizeAmount) {
         return rand(0, $randomizeAmount) % $randomizeAmount === 0;
      };

      foreach ($words as $word) {
         $newWord = $word;
         if ($isFun()) {
            $newWord = self::makeFunSpan($word);
         }
         $newText[] = $newWord;
      }
      return implode(' ', $newText);
   }
}
