<?php declare(strict_types=1);

namespace Utils;

class HtmlUtils {
   /**
    * NOTE: These element functions are silly, they're mostly just examples of
   * what you can do. There are better libraries that handle much more complicated
   * cases.
   */
   public static function makeImageElement($imgAttributes): string {
      $elPartStr = self::generateElementPartStr($imgAttributes);
      return "<img {$elPartStr} />";
   }

   public static function makeDivElement(string $text, array $elPartParams = []): string {
      $elPartStr = self::generateElementPartStr($elPartParams);
      return "<div {$elPartStr}>{$text}</div>";
   }

   public static function makePElement(string $text, array $elPartParams = []): string {
      $elPartStr = self::generateElementPartStr($elPartParams);
      return "<p {$elPartStr}>{$text}</p>";
   }

   public static function makeSpanElement(string $text, array $elPartParams): string {
      $elPartStr = self::generateElementPartStr($elPartParams);
      return "<span {$elPartStr}>{$text}</span>";
   }

   public static function makeH1Element(string $text, string $class): string {
      $elParts = ['class' => $class];
      return self::makeHXElement(1, $text, $elParts);
   }

   public static function makeH3Element(string $text, string $class = null): string {
      $elParts = $class ? ['class' => $class] : [];
      return self::makeHXElement(3, $text, $elParts);
   }

   public static function makeHXElement(int $hx, string $text, array $elPartParams = []): string {
      $elPartStr = self::generateElementPartStr($elPartParams);
      $startTag = "<h{$hx} {$elPartStr}>";
      $endTag = "</h{$hx}>";
      return "{$startTag}{$text}{$endTag}";
   }

   // This is unused but may be useful in the future.
   public static function makeUnorderList(array $listValues, $addDataMeta = false): string {
      $listElements = [];
      foreach($listValues as $lValue) {
         $elPartStr;
         if ($addDataMeta) {
            $elPartStr = self::generateElementPartStr(['data-value' => $lValue]);
         }
         $listElements[] = "<li {$elPartStr}>{$lValue}</li>";
      }
      $lElStr = implode(' ', $listElements);
      return "<ul>{$lElStr}</ul>";
   }

   public static function makePageWhitespace(): string {
      return '<br /><br />';
   }

   public static function makeTableElement(array $tableData): string {
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
      return self::makeSpanElement($text, ['class' => 'fun']);
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
   public static function makeFunSpanFromArray(array $textValues, string $iStr = ' '): string {
      $fun = [];
      foreach ($textValues as $text) {
         $fun[] = self::makeFunSpan($text);
      }
      return implode($iStr, $fun);
   }

   public static function addRandomFun(string $text, int $randomizeAmount): string {
      $words = explode(' ', $text);
      $newText = [];
      $isFun = function() use ($randomizeAmount) {
         if ($randomizeAmount < 1) {
            $randomizeAmount = 25; // arbitrary value but :shrug:
         }
         return rand(1, $randomizeAmount) % $randomizeAmount === 0;
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

   private static function generateElementPartStr(array $elParams): string {
      $elParts = [];
      foreach($elParams as $name => $value) {
         if ($value) {
            $elParts[] = "{$name}=\"{$value}\"";
         }
      }
      return implode(' ', $elParts);
   }
}
