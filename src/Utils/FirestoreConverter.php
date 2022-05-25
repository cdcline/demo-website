<?php declare(strict_types=1);

namespace Utils;

use DB\Firestore\Collection;
use Google\Cloud\Firestore\DocumentSnapshot;
use Google\Cloud\Firestore\QuerySnapshot;

class FirestoreConverter {
   const DOC_INDEX = 'docIndex';
   const SNAP_INDEX = 'snapIndex';
   const NEW_INDEX = 'newIndex';

   private $path;
   private $snapConverters;
   private $docConverters;

   public static function fromValues(string $path, array $docValues, array $snapValues, $convertFromFirestoreFnc = null): array {
      $dConverters = DocConverters::fromArray($docValues);
      $sConverters = SnapConverters::fromArray($snapValues);
      $converter = new self($path, $dConverters, $sConverters);
      return array_map($convertFromFirestoreFnc, Collection::convertToLegacyArray($converter));
   }

   public function getPath(): string {
      return $this->path;
   }

   public function toLegacyArray(QuerySnapshot $fDocuments): array {
      $rows = [];
      foreach ($fDocuments as $fDocument) {
         $rows[] = array_merge(
            ['firestoreId' => $fDocument->id()],
            $this->docConverters->convert($fDocument),
            $this->snapConverters->convert($fDocument)
         );
      };
      return $rows;
   }

   private function __construct(string $path, DocConverters $docConverters, SnapConverters $snapConverters) {
      $this->path = $path;
      $this->docConverters = $docConverters;
      $this->snapConverters = $snapConverters;
   }
}

class SnapConverters extends ConvertEngine {
   public static function fromArray(array $snapValues): self {
      $fromValues = function($sValue) {
         return SnapConverter::fromArray($sValue);
      };
      return new self(array_map($fromValues, $snapValues));
   }
}

class DocConverters extends ConvertEngine {
   public static function fromArray(array $snapValues): self {
      $fromValues = function($sValue) {
         return DocConverter::fromArray($sValue);
      };
      return new self(array_map($fromValues, $snapValues));
   }
}

abstract class ConvertEngine {
   protected $converters;

   protected function __construct(array $converters) {
      $this->converters = $converters;
   }

   public function convert(DocumentSnapshot $doc): array {
      $values = [];
      foreach ($this->converters as $converter) {
         $values = array_merge($values, $converter->convert($doc));
      }
      return $values;
   }
}

class SnapConverter extends IndexConverter {
   protected $snapIndex;

   public static function fromArray(array $iSnap): self {
      $docIndex = $iSnap[FirestoreConverter::DOC_INDEX];
      $snapIndex = $iSnap[FirestoreConverter::SNAP_INDEX];
      $newIndex = $iSnap[FirestoreConverter::NEW_INDEX] ?? null;
      return new self($docIndex, $snapIndex, $newIndex);
   }

   protected function getDocumentValue(DocumentSnapshot $doc) {
      return $doc[$this->docIndex]->snapshot()->data()[$this->snapIndex];
   }

   private function __construct(string $docIndex, string $snapIndex, string $newIndex = null) {
      $this->docIndex = $docIndex;
      $this->snapIndex = $snapIndex;
      $this->newIndex = $newIndex;
   }
}

class DocConverter extends IndexConverter {
   public static function fromArray($iDoc): self {
      $docIndex = is_array($iDoc) ? $iDoc[FirestoreConverter::DOC_INDEX] : $iDoc;
      $newIndex = is_array($iDoc) ? ($iDoc[FirestoreConverter::NEW_INDEX] ?? null) : null;
      return new self($docIndex, $newIndex);
   }

   protected function getDocumentValue(DocumentSnapshot $doc) {
      return $doc->get($this->docIndex);
   }

   private function __construct(string $docIndex, ?string $newIndex) {
      $this->docIndex = $docIndex;
      $this->newIndex = $newIndex;
   }
}

abstract class IndexConverter {
   protected $docIndex;
   protected $newIndex;

   abstract protected function getDocumentValue(DocumentSnapshot $doc);

   public function convert(DocumentSnapshot $doc): array {
      return [$this->getIndex() => $this->getDocumentValue($doc)];
   }

   private function getIndex(): string {
      return $this->newIndex ?: $this->docIndex;
   }
}
