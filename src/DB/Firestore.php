<?php declare(strict_types=1);

namespace DB;

use Google\Cloud\Firestore\FirestoreClient;

class Firestore {
   private $fClient;

   public static function fetchNewConnection(): self {
      return new self();
   }

   private function __construct() {
      $this->fClient = new FirestoreClient();
   }

   /**
    * Firestore is very different from MySQL for how it wants to do things.
    *
    * It really wants a UX interface to edit things & to be really slick it
    * doesn't even want to touch the backend I bet.
    *
    * But I have all the structures setup for MySQL and I'm not gonna do an edit
    * interface any time soon so we'll just hack in a shim for a bit.
    *
    * It'll have to be a little non-optimal (references will be kinda silly) but
    * it shoud work until we get around to doing the write part.
    */
   public function fakeOldDB(string $collectionName, array $params): array {
      $strIndexes = $params['strIndexes'] ?? [];
      // Sometimes we want to pull data from a "referenced collection."
      // We need to take a "snapshot of that collection" to get the value.
      // This is a very basic way of pulling a value from another referenced table.
      $snapIndexes = $params['snapIndexes'] ?? [];
      foreach ($this->getDocuments($collectionName) as $page) {
         $row['firestoreId'] = $page->id();
         foreach ($strIndexes as $i) {
            if (!empty($page[$i])) {
               $row[$i] = $page[$i];
            }
         }
         foreach ($snapIndexes as $sData) {
            $iRef = $sData['strIndex'];
            $iSnap = $sData['snapIndex'];
            $iNew = $sData['newIndex'] ?? $iRef;
            if (!empty($page[$iRef])) {
               $snapData = $page[$iRef]->snapshot()->data();
               $row[$iNew] = $snapData[$iSnap];
            }
         }
         $rows[] = $row;
      }
      return $rows;
   }

   private function getCollection($collectionName) {
      return $this->fClient->collection($collectionName);
   }

   private function getDocuments($collectionName) {
      $collection = $this->getCollection($collectionName);
      return $collection ? $collection->documents() : [];
   }
}
