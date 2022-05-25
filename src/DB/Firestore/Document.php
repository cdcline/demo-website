<?php declare(strict_types=1);

namespace DB\Firestore;

use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\Query;

class Document {
   private $fClient;

   public static function fetchNewConnection(): self {
      return new self();
   }

   private function __construct() {
      $this->fClient = new FirestoreClient();
   }

   public function getCollection($collectionName): Query {
      return $this->fClient->collection($collectionName);
   }
}
