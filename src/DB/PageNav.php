<?php declare(strict_types=1);

namespace DB;

use DB\DBTrait;
use Exception;
use Pages\InvalidPageException;
use Utils\StringUtils;
use DB\Firestore\Collection;

class PageNav {
   use DBTrait;

   public const ARTICLE_PAGE_TYPE = 'ARTICLE_PAGE';
   public const CUSTOM_TYPE = 'CUSTOM';
   public const DEFAULT_PAGEID = 1;

   private const COLLECTION_NAME = 'page_nav';

   private $slug;
   private $nav_string;
   private $orderby;
   // Odd to need a pageid here but we use it to generate the link URLs
   private $pageid;
   private $type;

   public static function getPageidFromSlug(string $slug): int {
      foreach (self::fetchAllRowsFromStaticCache() as $pNav) {
         if ($pNav->matchesSlug($slug)) {
            return $pNav->getPageid();
         }
      }
      InvalidPageException::throwPageNotFound($slug);
   }

   public static function getDefaultSlug(): string {
      foreach (self::fetchAllRowsFromStaticCache() as $pNav) {
         if ($pNav->matchesPageid(self::DEFAULT_PAGEID)) {
            return $pNav->getSlug();
         }
      }
      throw new Exception('Default page not configured correctly. Unkown HOMEPAGE_PAGEID.');
   }

   public static function fromArray(array $aData) {
      return new self($aData['slug'], $aData['nav_string'], $aData['type'], (int)$aData['orderby'], (int)$aData['pageid']);
   }

   public function toArray(): array {
      return [
         'type' => $this->type,
         'slug' => $this->slug,
         'nav_string' => $this->navString,
         'pageid' => $this->pageid,
         'orderby' => $this->orderby
      ];
   }

   private static function fetchAllRows(): array {
      $path = 'page_nav';
      $dValues = ['slug', 'nav_string', 'orderby'];
      $sValues =[
         ['docIndex' => 'page', 'snapIndex' => 'pageid', 'newIndex' => 'pageid'],
         ['docIndex' => 'n_type', 'snapIndex' => 'enum', 'newIndex' => 'type'],
      ];
      return array_map(
         fn($aValues) => self::fromArray($aValues),
         Collection::getValuesFromPath($path, $dValues, $sValues)
      );
   }

   private function __construct(string $slug, string $navString, string $type, int $orderby, int $pageid) {
      $this->slug = $slug;
      $this->navString = $navString;
      $this->type = $type;
      $this->orderby = $orderby;
      $this->pageid = $pageid;
   }

   private function getSlug() {
      return $this->slug;
   }

   private function getPageid(): int {
      return $this->pageid;
   }

   private function matchesPageid(int $pageid): bool {
      return $this->pageid === $pageid;
   }

   private function matchesSlug(string $slug): bool {
      return StringUtils::iMatch($this->slug, $slug);
   }

   // NOTE: Order of the data matters, should match `fetchAllRows`
   private static function getHardcodedRows(): array {
      $values = [
         ['navid' => 1,
          'type' => self::ARTICLE_PAGE_TYPE,
          'slug' => 'homepage',
          'nav_string' => 'Homepage',
          'pageid' => 1,
          'orderby' => 1
         ],
         ['navid' => 2,
          'type' => self::CUSTOM_TYPE,
          'slug' => 'https://github.com/cdcline/demo-website',
          'nav_string' => 'Resume',
          'pageid' => NULL,
          'orderby' => 3
         ],
         ['navid' => 3,
          'type' => self::ARTICLE_PAGE_TYPE,
          'slug' => 'dev',
          'nav_string' => 'Dev',
          'pageid' => 2,
          'orderby' => 2
         ]
      ];
      return array_map(fn($vals) => self::fromArray($vals), $values);
   }
}
