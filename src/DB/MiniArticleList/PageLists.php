<?php declare(strict_types=1);

namespace DB\MiniArticleList;

use DB\DBTrait;
use DB\MiniArticleList\ArticleCollection;
// use Exception;
// use Utils\FirestoreUtils;

class PageLists {
   private $miniArticleLists;
   use DBTrait;

   public static function fetchAll(): array {
      $lists = self::fromValues(self::fetchAllRowsFromStaticCache());
      return $lists->toArray();
   }

   public static function forPageid(int $pageid) {
      $lists = self::fromValues(self::fetchAllRowsFromStaticCache());
      return $lists->filterByPageid($pageid);
   }

   private static function fromValues(array $listValues) {
      $pageLists = [];
      foreach ($listValues as $list) {
         $pageLists[] = ArticleCollection::fromValues($list['title'], $list['articles']);
      }
      return new self($pageLists);
   }

   private function __construct(array $miniArticleLists) {
      $this->miniArticleLists = $miniArticleLists;
   }

   private function toArray(): array {
      $tagToArray = function($maList) {
         return $maList->toArray();
      };
      return array_map($tagToArray, $this->miniArticleLists);
   }

   private function filterByPageid(int $pageid): array {
      $lists = $this->miniArticleLists;
      $onPageid = function($maList) use ($pageid) {
         return $maList->onPageid($pageid);
      };
      $pageLists = array_filter($lists, $onPageid);
      return $pageLists;
   }

   private static function fetchAllRows(): array {
      return [];//self::getMiniArticles();
   }

   /*
   private static function getMiniArticles(): array {
      // We're gonna pull these values from each `articles` collection
      $aDocValues = ['title', 'text', 'start_date', 'end_date'];
      // There might be several "mini article lists" to iterate over
      foreach (self::getMiniArticleCollections() as $maList) {
         $pageid = $maList['pageid'];
         $articlesPath = $maList['articlesPath'];
         // We have to do a lot of things after we fetch the `articles` collection
         $handleRow = function($article) use ($articlesPath, $pageid) {
            return self::convertFromFirestore($articlesPath, $pageid, $article);
         };
         $cArticles = array_map($handleRow, self::fetchRows($articlesPath, $aDocValues));
         $maLists[] = [
            'title' => $maList['title'],
            'articles' => $cArticles
         ];
      }
      return $maLists ?: [];
   }

   private static function convertFromFirestore(string $articlesPath, int $pageid, array $article) {
      $fromGoogleTime = function($index) use ($article): int {
         $gDate = $article[$index] ?? null;
         return $gDate ? $gDate->get()->getTimestamp() : 0;
      };
      return [
         'firestoreId' => $article['firestoreId'],
         'title' => $article['title'] ?? 'Default Title',
         'text' => FirestoreUtils::hackNewlines($article['text'] ?? ''),
         'start_date' => $fromGoogleTime('start_date'),
         'end_date' => $fromGoogleTime('end_date'),
         'tags' =>  self::getTags($articlesPath, $article['firestoreId']),
         'pageid' =>  $pageid,
      ];
   }

   private static function getTags($articleListPath, $firestoreId): array {
      // Generate the tag collection path for the given article_list
      $tPath = FirestoreUtils::tagPath($articleListPath, $firestoreId);
      // We want the "enum" value but assign it to the "tag" index
      $sValues = [FirestoreUtils::buildSnap('tag', 'enum')];
      // We want to return a single array with the "enum" values
      return array_column(self::fetchRows($tPath, [], $sValues), 'tag');
   }

   // We want to find all the mini_article_list collections, with the pageid from
   // the page_index table also merged in
   private static function getMiniArticleCollections(): array {
      $maCollections = [];
      // We're gonna pull the "title" field from each mini_article_list collection
      $maCollectionsDocValues = ['title'];
      // Go through all our index pages and grab the firestoreid and the "pageid"
      $iPagesPath = FirestoreUtils::indexPagesPath();
      $iDocs = ['pageid'];
      foreach (self::fetchRows($iPagesPath, $iDocs) as $iPage) {
         $fId = $iPage['firestoreId'];
         $pageid = $iPage['pageid'];
         $maCollectionsPath =  FirestoreUtils::maPath($fId);
         // All collections want the page_index `pageid` for now
         $gData = ['pageid' => $pageid];
         try {
            // Grab Mini Article List collections & add some data to it
            foreach ( self::fetchRows($maCollectionsPath, $maCollectionsDocValues) as $cListData) {
               // We'll generate the articles path here b/c it's convienient
               $articlesPath = FirestoreUtils::articlesPath($maCollectionsPath, $cListData['firestoreId']);
               $maCollections[] = array_merge($cListData, $gData, ['articlesPath' => $articlesPath]);
            }
         } catch (Exception $e) {}
      }
      return $maCollections;
   }
   */


   private static function getDevStaticData(): array {
      return self::getDevHardcodedRows();
   }

   private static function getLiveStaticData(): array {
      return self::getLiveHardcodedRows();
   }

   private static function getDevHardcodedRows(): array {
      return [
         ['title' => 'Mini Article List!',
          'articles' => [
            ['pageid' => 2,
            'title' => 'Mini Article 1',
            'text' => self::getStaticArticleText(1),
            'start_date' => 1391555735,
            'end_date' => 1645658135,
            'tags' => ['Foo','Buzz','☃️']
            ],
            ['pageid' => 2,
            'title' => 'Mini Article 2',
            'text' => self::getStaticArticleText(2),
            'start_date' => 1325546135,
            'end_date' => NULL,
            'tags' => ['Foo','Bar']
            ],
            ['pageid' => 2,
            'title' => 'Mini Article 5',
            'text' => self::getStaticArticleText(3),
            'start_date' => 1650774278,
            'end_date' => 1713932680,
            'tags' => ['Foo','Bar','Fizz','Buzz','🎂']
            ],
            ['pageid' => 2,
            'title' => 'Mini Article 3',
            'text' => self::getStaticArticleText(4),
            'start_date' => 409674412,
            'end_date' => 447179274,
            'tags' => ['Fizz','Buzz']
            ],
            ['pageid' => 2,
            'title' => 'Mini Article 4',
            'text' => self::getStaticArticleText(5),
            'start_date' => 930049271,
            'end_date' => NULL,
            'tags' => ['Fizz','Bar','☃️']
            ],
          ]
         ]
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

   private static function getLiveHardcodedRows(): array {
      return [
         ['title' => 'Projects',
          'articles' => [
            ['pageid' => 2,
            'title' => 'AJAX Shopping Cart',
            'text' => self::getLiveArticleText(1),
            'start_date' => 1167642061,
            'end_date' => 1577869261,
            'tags' => ['System Design', 'QA', 'PHP', 'HTML', 'JS', 'MySQL']
            ],
            ['pageid' => 2,
            'title' => 'The "Customer Manager"',
            'text' => self::getLiveArticleText(2),
            'start_date' => 1167642061,
            'end_date' => 1650067261,
            'tags' => ['System Design', 'PHP', 'HTML', 'JS', 'MySQL']
            ],
            ['pageid' => 2,
            'title' => 'Cart Admin Systems',
            'text' => self::getLiveArticleText(3),
            'start_date' => 1167642061,
            'end_date' => 1650067261,
            'tags' => ['System Design', 'PHP', 'HTML', 'JS', 'MySQL']
            ],
            ['pageid' => 2,
            'title' => 'Payments',
            'text' => self::getLiveArticleText(4),
            'start_date' => 1167642061,
            'end_date' => 1577869261,
            'tags' => ['System Design', 'PHP', 'XML', 'MySQL']
            ],
            ['pageid' => 2,
            'title' => 'Users',
            'text' => self::getLiveArticleText(5),
            'start_date' => 1167642061,
            'end_date' => 1650067261,
            'tags' => ['System Design', 'PHP', 'XML', 'MySQL']
            ],
            ['pageid' => 2,
            'title' => 'NASA Regolith Excavation Challenge',
            'text' => self::getLiveArticleText(6),
            'start_date' => 1167642061,
            'end_date' => 1650067261,
            'tags' => ['System Design', 'Robots', 'C']
            ],
            ['pageid' => 2,
            'title' => 'Tractor Hacking',
            'text' => self::getLiveArticleText(7),
            'start_date' => 1167642061,
            'end_date' => 1650067261,
            'tags' => ['System Design', 'Management', 'Agile']
            ],
            ['pageid' => 2,
            'title' => 'Mentoring',
            'text' => self::getLiveArticleText(8),
            'start_date' => 1167642061,
            'end_date' => 1650067261,
            'tags' => ['Management', 'Agile']
            ],
          ]
         ]
      ];
   }

   private static function getLiveArticleText(int $articleid): string {
      switch($articleid) {
         case 1: return <<<EOT
My first task as a Software Developer was testing a newly implimented shopping cart.

It had all the wizbangs and featured AJAX requests! Finally, you didn't have to load an entire page to add a new item to the cart. Just update a small section instead!

Over time, our custom homegrown shopping cart became overly complicated, and I had to manage those changes.

I built cart pages, checkout experiences, user address management, and product navigation.

I collaborated with designers to ensure designs fit easily into what was built and what we were building.
EOT;
         case 2: return <<<EOT
I was first lured to work by the offer of a "Paid Senior Project."

This ended up being a backend CMS that aggregated, tracked, and managed customer orders, returns, information, tags, and other random utilities.

It was generally used by Customer Service to manage order issues.
EOT;
         case 3: return <<<EOT
In addition to working on the cart, I also maintained a lot of various backend pages.

For Example:
   * New/Used Inventory Input
   * Inventory History
   * Returns
   * Shipping Labels
   * Product Lists
   * Product Attributes
EOT;
         case 4: return <<<EOT
When we first built the cart, we had a "custom" payment processor that enabled us to charge, debit, and refund through XML.

As time progressed, newer payment processors became available and we began implementing them.

I added support for:
 * Stripe
 * PayPal
 * ApplePay

In another project, I built an "Automated Recurring Billing System" that ran off of a CronJob that charged customers monthly.
EOT;
         case 5: return <<<EOT
Over time, we supported features unrelated to checkouts and orders. We needed "user" information not "customer" information.

Since I had a lot of experience working on "customer" code, I was tasked with a lot of "user" features.

For Example:
   * View/Edit Profile Information
   * View/Edit User Profile Page
   * Admin User Utilities
   * Moderation
   * Filtering
   * Ordering

I also implemented logins from external sources:
   * Yahoo
   * Google
   * PayPal
   * Apple
EOT;
         case 6: return <<<EOT
I was the only programmer on a team tasked with designing and building a robot that woke up in an unknown position, dug out "regolith,"  dumped it in a box, and navigated around obstacles.

We won second place but no one completed the core challenge of digging a specific amount of regolith.

The second time we were self funded but had a much better machine. We completed the challenge weeks before the competition — and well past the minimum dig amount.

(However, our dig motor blew out on our final run.)
EOT;
         case 7: return <<<EOT
My company sponsored a Cal Poly student team to create software that allowed farmers to manage their tractor information without needing the manufacturer.

I was in charge of giving a presentation of the project to classes, where students could choose to work on it.

Once students joined, we had a small team that organized around "Agile Development" and I was the "Project Owner."

I planned sprint objectives and participated in Retrospectives to give direction.

The final student team created a business from this project and even received funding.
EOT;
         case 8: return <<<EOT
We often hired students who worked for a few years then moved on after they graduate. I often worked with them to explain our deploy pipline and complex logic.

I kept an eye on "Project Scopes" so I could help break them up into known chunks of effort we could estimate in a sprint.
EOT;
      }
   }
}
