<?php declare(strict_types=1);

namespace DB\Firestore;

use DB\Firestore\Document as FirestoreDocument;
use Google\Cloud\Firestore\Query;
use Google\Cloud\Firestore\QuerySnapshot;
use InvalidArgumentException;
use Utils\FirestoreConverter as FirestoreConverter;

class Collection {
   private $collection;
   private $documents;

   public static function getValuesFromPath(string $path, array $docValues = [], array $snapValues = []): array {
      // Grab a collection of documents from Firestore
      $c = self::fromPath($path);
      // Go through the collection and pull out any "document" values
      // NOTE: There's always at least one document value; the id.
      $vDocs = $c->getDocumentValues($docValues);
      // Go through the collection and pull out any "snapshot" values
      $sDocs =  $c->getSnapshotValues($snapValues);
      $vals = [];
      foreach ($vDocs as $docId => $docVals) {
         // Check & merge any snapshot values in
         $sVals = $sDocs[$docId] ?? [];
         $vals[$docId] = $sVals ? array_merge($docVals, $sVals) : $docVals;
      }
      return $vals;
   }

   public static function convertToLegacyArray(FirestoreConverter $fConverter): array {
      $fCollection = self::fromPath($fConverter->getPath());
      return $fConverter->toLegacyArray($fCollection->getDocuments());
   }

   private static function fromPath(string $path): self {
      $fCollection = FirestoreDocument::fetchNewConnection()->getCollection($path);
      return new self($fCollection);
   }

   private function getDocumentValues(array $dValues): array {
      $aDocs = [];
      foreach($this->getDocuments() as $document) {
         $id = $document->id();
         $aDoc = ['firestoreId' => $id];
         foreach ($dValues as $i) {
            try {
               $iVal = $document->get($i);
            } catch (InvalidArgumentException $e) {
               $iVal = null;
            }
            $aDoc[$i] = $iVal;
         }
         $aDocs[$id] = $aDoc;
      }
      return $aDocs;
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

   private function getDocuments(): QuerySnapshot {
      return $this->documents ?? ($this->documents = $this->collection->documents());
   }

   private function __construct(Query $cQuery) {
      $this->collection = $cQuery;
   }
}
