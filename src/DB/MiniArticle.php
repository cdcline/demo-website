<?php declare(strict_types=1);

namespace DB;

use DB\DBTrait;

class MiniArticle {
   use DBTrait;

   private const GROUP_CONCAT_INDEX = 'tags';
   private const GROUP_CONCAT_TOKEN = ',';

   public static function fetchAll($breakUpGroupConcat = true): array {
      $rows = self::fetchAllRowsFromStaticCache();
      return $breakUpGroupConcat ?
       self::breakUpGroupConcat($rows, self::GROUP_CONCAT_INDEX) :
       $rows;
   }

   // We'd rather work with arrays and might as well do it early
   // This is def a KISS implementation
   private static function breakUpGroupConcat(array $rows, string $bIndex): array {
      foreach ($rows as $rIndex => $row) {
         $rows[$rIndex][$bIndex] = explode(self::GROUP_CONCAT_TOKEN, $row[$bIndex]);
      }
      return $rows;
   }

   /**
    * On one hand we're smashing 3 tables into a single query, none of queries
    * are really useable elsewhere & we're creating many rows when we only need
    * one for a single mini article page.
    *
    * On the other KISS!
    *
    * NOTE: We'll probably update this to not grab ALL the things at some point
    * but the data set is so low it really shouldn't matter.
    */
   private static function fetchAllRows(): array {
      $sql = <<<EOT
      SELECT `pageid`, `title`, `mini_article`.`text` as `mini_article_text`,
             `start_date`, `end_date`, GROUP_CONCAT(`tag`.`text`) as `tags`
      FROM `mini_article_tag`
      JOIN (`tag`) USING (`tagid`)
      JOIN `mini_article` USING (`mini_articleid`)
      GROUP BY `mini_articleid`
EOT;
      return $this->db()->fetchAll($sql);
   }

   private static function getStaticRows(): array {
      return [
         ['pageid' => 2,
         'title' => 'Mini Article 1',
         'mini_article_text' => self::getStaticArticleText(1),
         'start_date' => 1391555735,
         'end_date' => 1645658135,
         'tags' => 'Foo,Buzz,☃️'
         ],
         ['pageid' => 2,
         'title' => 'Mini Article 2',
         'mini_article_text' => self::getStaticArticleText(2),
         'start_date' => 1325546135,
         'end_date' => NULL,
         'tags' => 'Foo,Bar'
         ],
         ['pageid' => 2,
         'title' => 'Mini Article 5',
         'mini_article_text' => self::getStaticArticleText(3),
         'start_date' => 1650774278,
         'end_date' => 1713932680,
         'tags' => 'Foo,Bar,Fizz,Buzz,🎂'
         ],
         ['pageid' => 2,
         'title' => 'Mini Article 3',
         'mini_article_text' => self::getStaticArticleText(4),
         'start_date' => 409674412,
         'end_date' => 447179274,
         'tags' => 'Fizz,Buzz'
         ],
         ['pageid' => 2,
         'title' => 'Mini Article 4',
         'mini_article_text' => self::getStaticArticleText(5),
         'start_date' => 930049271,
         'end_date' => NULL,
         'tags' => 'Fizz,Bar,☃️'
         ],
      ];
   }

   private static function getStaticArticleText(int $articleid): string {
      switch($articleid) {
         case 1: return <<<EOT
##### This is a _mini_ article!
It's a small about of text with some tag and date data asscociated with it!
Sit amet nulla facilisi morbi tempus iaculis urna. Ullamcorper a lacus vestibulum sed arcu. Ligula ullamcorper malesuada proin libero nunc consequat. Mattis aliquam faucibus purus in massa tempor nec feugiat. Maecenas ultricies mi eget mauris pharetra et ultrices. Pharetra diam sit amet nisl suscipit adipiscing bibendum. Et ligula ullamcorper malesuada proin libero. Tellus elementum sagittis vitae et leo. Sagittis nisl rhoncus mattis rhoncus urna neque. At urna condimentum mattis pellentesque id nibh tortor.
EOT;
         case 2: return <<<EOT
##### This is another _mini_ article!
Felis donec et odio pellentesque diam volutpat commodo sed. Habitasse platea dictumst quisque sagittis purus sit amet volutpat. Urna nunc id cursus metus aliquam eleifend mi. Morbi tempus iaculis urna id volutpat lacus laoreet. Sollicitudin tempor id eu nisl nunc mi ipsum faucibus. Tortor vitae purus faucibus ornare suspendisse sed nisi lacus. Enim nunc faucibus a pellentesque. Feugiat vivamus at augue eget. Eget felis eget nunc lobortis mattis aliquam faucibus. Tortor id aliquet lectus proin nibh nisl condimentum. Egestas maecenas pharetra convallis posuere morbi leo urna molestie. Id aliquet lectus proin nibh nisl condimentum id venenatis. Libero justo laoreet sit amet. Dignissim sodales ut eu sem integer vitae justo eget. Mi bibendum neque egestas congue quisque egestas diam. Tempus urna et pharetra pharetra massa massa ultricies mi quis. Id diam maecenas ultricies mi eget mauris pharetra et ultrices. Eget nunc lobortis mattis aliquam faucibus purus in massa.
EOT;
         case 3: return <<<EOT
##### This is **the** mini _article_!
Nisi est sit amet facilisis. Rutrum quisque non tellus orci ac. Mauris in aliquam sem fringilla ut. Venenatis lectus magna fringilla urna. Dictumst vestibulum rhoncus est pellentesque elit. Rhoncus est pellentesque elit ullamcorper dignissim cras. Eros in cursus turpis massa tincidunt dui. At augue eget arcu dictum varius duis at consectetur lorem. Dui ut ornare lectus sit amet est placerat. Semper viverra nam libero justo laoreet. At ultrices mi tempus imperdiet nulla malesuada pellentesque. Nullam non nisi est sit amet. Metus vulputate eu scelerisque felis imperdiet proin. Porttitor eget dolor morbi non arcu risus quis. Quis ipsum suspendisse ultrices gravida dictum. Tellus molestie nunc ma yafort ayber die partea rox.
EOT;
         case 4: return <<<EOT
##### **This** is a _mini_ article!
Metus aliquam eleifend mi in. Fermentum iaculis eu non diam phasellus vestibulum lorem sed. Non pulvinar neque laoreet suspendisse interdum consectetur. Condimentum vitae sapien pellentesque habitant morbi tristique senectus et netus. Posuere ac ut consequat semper viverra. Tortor consequat id porta nibh. Maecenas volutpat blandit aliquam etiam erat. Ut faucibus pulvinar elementum integer enim. Nisl rhoncus mattis rhoncus urna. Id nibh tortor id aliquet lectus proin nibh nisl condimentum. Vulputate ut pharetra sit amet aliquam id. Volutpat ac tincidunt vitae semper. Sagittis id consectetur purus ut faucibus pulvinar elementum. Donec adipiscing tristique risus nec feugiat in. In ante metus dictum at tempor commodo ullamcorper a. Netus et malesuada fames ac turpis egestas maecenas pharetra.
EOT;
         case 5: return <<<EOT
##### This is ~~not~~ a _mini_ article!
`Tellus mauris a diam maecenas sed enim ut. Dui vivamus arcu felis bibendum ut tristique et egestas. Ante in nibh mauris cursus mattis. Euismod elementum nisi quis eleifend quam. Sollicitudin aliquam ultrices sagittis orci a scelerisque purus. Eget sit amet tellus cras adipiscing enim eu turpis egestas. Bibendum ut tristique et egestas. Facilisi morbi tempus iaculis urna id volutpat lacus laoreet. Nullam non nisi est sit amet facilisis magna etiam tempor. Nisi vitae suscipit tellus mauris a diam maecenas. Dignissim sodales ut eu sem integer. Vitae congue eu consequat ac felis donec et odio pellentesque. Vitae congue eu consequat ac felis donec et odio. Potenti nullam ac tortor vitae purus faucibus.`
EOT;
      }
   }
}
