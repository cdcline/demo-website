<?php declare(strict_types=1);

namespace DB;

use DB\DBTrait;

class PageIndex {
   const DEFAULT_TYPE = 'default';
   const DEV_TYPE = 'dev';

   use DBTrait;

   public static function getTypeFromPageid(int $pageid): ?string {
      foreach (self::fetchAllRowsFromStaticCache() as $row) {
         if ($row['pageid'] == $pageid) {
            return $row['type'];
         }
      }
      return null;
   }

   private static function fetchAllRows(): array {
      $sql = <<<EOT
         SELECT `pageid`, `page_title`, `page_header`, `main_article`
         FROM `page_index`
EOT;
      return self::db()->fetchAll($sql);
   }

   private static function getStaticRows(): array {
      return [
         ['pageid' => 1,
          'page_title' => 'About Me - Website Demo',
          'page_header' => 'About Me',
          'type' => self::DEFAULT_TYPE,
          'main_article' => <<<EOT
## This is the About Me Article!

I write code and don't have _any_ coding examples. I hope this will serve both as my personal website and an example of how I write code!
EOT
         ],
         ['pageid' => 2,
          'page_title' => 'Dev - Website Demo',
          'page_header' => 'The Dev Environment',
          'type' => self::DEV_TYPE,
          'main_article' => <<<EOT
## This is the Dev Article!

I need a space that's pretty constant and one that's _kinda_ scratch paper. This one's the scratch paper!
EOT
         ],
         ['pageid' => 3,
          'page_title' => 'Test 3 - Website Demo',
          'page_header' => 'Test Page 3',
          'type' => self::DEV_TYPE,
          'main_article' => <<<EOT
## This is **Test Page 3**

Egestas sed tempus urna et pharetra pharetra massa massa ultricies. Neque sodales ut etiam sit amet nisl. Dictum sit amet justo donec enim diam vulputate. Morbi tincidunt augue interdum velit euismod in pellentesque massa placerat. Vulputate enim nulla aliquet porttitor. Aenean et tortor at risus viverra adipiscing. Pharetra sit amet aliquam id diam. Platea dictumst vestibulum rhoncus est pellentesque elit ullamcorper dignissim. Adipiscing at in tellus integer feugiat. Nulla facilisi cras fermentum odio eu feugiat pretium nibh. Pharetra massa massa ultricies mi quis hendrerit dolor. Purus ut faucibus pulvinar elementum integer enim neque volutpat ac.
EOT
         ],
         ['pageid' => 4,
          'page_title' => 'Test 4 - Website Demo',
          'page_header' => 'Test Page 4',
          'type' => self::DEFAULT_TYPE,
          'main_article' => <<<EOT
## This is _Test Page 4_

Vulputate dignissim suspendisse in est. Amet risus nullam eget felis eget nunc lobortis. Pellentesque diam volutpat commodo sed egestas. Id leo in vitae turpis massa sed elementum tempus egestas. Nam libero justo laoreet sit amet cursus sit. Consectetur purus ut faucibus pulvinar. Laoreet suspendisse interdum consectetur libero id faucibus nisl. Laoreet non curabitur gravida arcu ac tortor dignissim convallis aenean. Viverra mauris in aliquam sem fringilla. Nibh nisl condimentum id venenatis a condimentum vitae sapien pellentesque. Ut placerat orci nulla pellentesque. Bibendum at varius vel pharetra vel turpis nunc eget lorem. Euismod quis viverra nibh cras pulvinar mattis nunc sed.
EOT
         ],
      ];
   }
}
