<?php declare(strict_types=1);

namespace DB\Firestore;

use DB\Firestore\Client as FirestoreClient;
use Google\Cloud\Firestore\CollectionReference;

class Collection {
   private $collection;
   private $documents;

   public static function getValuesFromPath(string $path, array $docValues = [], array $snapValues = []): array {
      $c = Collection::fromPath($path);
      $vDocs = $c->getDocumentValues($docValues);
      $sDocs =  $c->getSnapshotValues($snapValues);
      $vals = [];
      foreach ($vDocs as $docId => $docVals) {
         if (isset($sDocs[$docId])) {
            $vals[] = array_merge($docVals, $sDocs[$docId]);
         }
      }
      return $vals;
   }

   private static function fromPath(string $path): self {
      $fClient = FirestoreClient::fetchNewConnection()->getCollection($path);
      return new self($fClient);
   }

   private function getSnapshotValues(array $sValues): array {
      $sDocs = [];
      foreach($this->getDocuments() as $document) {
         $id = $document->id();
         foreach ($sValues as $sData) {
            $iRef = $sData['docIndex'];
            $iSnap = $sData['snapIndex'];
            $iNew = $sData['newIndex'] ?? $iRef;
            $row[$iNew] = null;
            if (!empty($document[$iRef])) {
               $snapData = $document[$iRef]->snapshot()->data();
               $row[$iNew] = $snapData[$iSnap];
            }
         }
         $sDocs[$id] = $row;
      }
      return $sDocs;
   }

   private function getDocumentValues(array $dValues): array {
      $aDocs = [];
      foreach($this->getDocuments() as $document) {
         $id = $document->id();
         $aDoc = ['firestoreId' => $id];
         foreach ($dValues as $i) {
            $aDoc[$i] = $document->get($i);
         }
         $aDocs[$id] = $aDoc;
      }
      return $aDocs;
   }

   private function getDocuments() {
      return $this->documents ?? ($this->documents = $this->collection->documents());
   }

   private function __construct(CollectionReference $collection) {
      $this->collection = $collection;
   }
}
