<?php declare(strict_types=1);

namespace DB;

use DB\DBTrait;
use Pages\InvalidPageException;

class PageIndex {
   use DBTrait;

   const DEFAULT_TYPE = 'default';
   const HOMEPAGE_TYPE = 'homepage';
   const DEV_TYPE = 'dev';

   public static function getTypeFromPageid(int $pageid): string {
      return self::getRowFromPageid($pageid)['type'];
   }

   public static function getMainArticleTextFromPageid(int $pageid): string {
      return self::getRowFromPageid($pageid)['main_article'];
   }

   private static function getRowFromPageid(int $pageid): array {
      foreach (self::fetchAllRowsFromStaticCache() as $row) {
         if ($row['pageid'] == $pageid) {
            return $row;
         }
      }
      InvalidPageException::throwPageNotFound($pageid);
   }

   private static function fetchAllRows(): array {
      $fParams = [
      'strIndexes' => ['pageid', 'main_article', 'page_header', 'page_title'],
      'snapIndexes' => [['strIndex' => 'type', 'snapIndex' => 'name']]
      ];
      return self::fetchFireRows('page_index', $fParams);
   }

   // NOTE: Order of the data matters, should match `fetchAllRows`
   private static function getHardcodedRows(): array {
      return [
         ['type' => self::HOMEPAGE_TYPE,
          'pageid' => 1,
          'page_title' => 'Welcome - My Demo Website',
          'page_header' => 'Welcome',
          'main_article' => <<<EOT
## Welcome to My Personal Website!

I've been [writing code](#link) since [elementry school](https://en.wikipedia.org/wiki/Logo_(programming_language))!

I write code and didn't have _any_ coding examples or even a server to run my site on! I hope to solve this with My Personal Website. It will be both as my personal coding playground and an example of how I write code!

#### This page is an example of "My Demo Website" capabilities!

All the text you've read so far is **[parsable](#toggleParser)** _text_ and should be easy for anyone to ~~etid~~ edit.

This is great for:
* Speed
 * You can just write text and not worry about html
* Frequent updating
 * Easy to find the text to change
* Non-Coders
 * Don't have to know anything about CSS or HTML
* Readability
 * It's a block of text with kinda random punctuation.
 * No `<code>` elements to worry about.

However, it has it's limits. You can't really do fancy **frontend** _things_.
EOT
         ],
         ['type' => self::DEV_TYPE,
          'pageid' => 2,
          'page_title' => 'Dev - Website Demo',
          'page_header' => 'The Dev Environment',
          'main_article' => <<<EOT
## This is the Dev Article!

I need a space that's pretty constant and one that's _kinda_ scratch paper. This one's the scratch paper!
EOT
         ],
         ['type' => self::DEV_TYPE,
          'pageid' => 3,
          'page_title' => 'Test 3 - Website Demo',
          'page_header' => 'Test Page 3',
          'main_article' => <<<EOT
## This is **Test Page 3**

Egestas sed tempus urna et pharetra pharetra massa massa ultricies. Neque sodales ut etiam sit amet nisl. Dictum sit amet justo donec enim diam vulputate. Morbi tincidunt augue interdum velit euismod in pellentesque massa placerat. Vulputate enim nulla aliquet porttitor. Aenean et tortor at risus viverra adipiscing. Pharetra sit amet aliquam id diam. Platea dictumst vestibulum rhoncus est pellentesque elit ullamcorper dignissim. Adipiscing at in tellus integer feugiat. Nulla facilisi cras fermentum odio eu feugiat pretium nibh. Pharetra massa massa ultricies mi quis hendrerit dolor. Purus ut faucibus pulvinar elementum integer enim neque volutpat ac.
EOT
         ],
         ['type' => self::DEFAULT_TYPE,
          'pageid' => 4,
          'page_title' => 'Test 4 - Website Demo',
          'page_header' => 'Test Page 4',
          'main_article' => <<<EOT
## This is _Test Page 4_

Vulputate dignissim suspendisse in est. Amet risus nullam eget felis eget nunc lobortis. Pellentesque diam volutpat commodo sed egestas. Id leo in vitae turpis massa sed elementum tempus egestas. Nam libero justo laoreet sit amet cursus sit. Consectetur purus ut faucibus pulvinar. Laoreet suspendisse interdum consectetur libero id faucibus nisl. Laoreet non curabitur gravida arcu ac tortor dignissim convallis aenean. Viverra mauris in aliquam sem fringilla. Nibh nisl condimentum id venenatis a condimentum vitae sapien pellentesque. Ut placerat orci nulla pellentesque. Bibendum at varius vel pharetra vel turpis nunc eget lorem. Euismod quis viverra nibh cras pulvinar mattis nunc sed.
EOT
         ],
      ];
   }
}
