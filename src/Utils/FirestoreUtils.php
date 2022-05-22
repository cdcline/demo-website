<?php declare(strict_types=1);

namespace Utils;

use InvalidArgumentException;

class FirestoreUtils {
   private const PAGE_INDEX = 'page_index';
   private const ARTICLE_LISTS = 'mini_article_list';
   private const ARTICLES = 'articles';
   private const TAGS = 'tags';

   public static function maPath(string $firestoreId): string {
      return self::getPath('page_article_lists', ['id' => $firestoreId]);
   }

   public static function articlesPath(string $parent, string $id): string {
      return self::buildPath([$parent, $id, self::ARTICLES]);
   }

   public static function tagPath(string $parent, string $id): string {
      return self::buildPath([$parent, $id, self::TAGS]);
   }

   public static function hackNewlines(string $text) {
      return implode("\n\n", explode('[newline]', $text));
   }

   // NOTE: Id is short for "firestoreId".
   //       Everything here is in relation to firestore id's.
   //       Other "id" references should probably be removed at some point
   private static function getPath(string $slug, array $options = []): string {
      $id = $options['id'] ?? null;

      switch($slug) {
         case 'pages':
            return self::PAGE_INDEX;
         case 'page':
            if ($id) {
               return self::buildPath([self::PAGE_INDEX, $id]);
            }
         case 'page_article_lists':
            if ($id) {
               return self::buildPath([self::PAGE_INDEX, $id, self::ARTICLE_LISTS]);
            }
      }
      $optStr = '';
      if ($options) {
         $optStr = "\nOptions: " . print_r($options, true);
      }
      throw new InvalidArgumentException("Unable to find {$slug}[$optStr}");
   }

   private static function buildPath(array $pathParts): string {
      return implode('/', $pathParts);
   }
}
