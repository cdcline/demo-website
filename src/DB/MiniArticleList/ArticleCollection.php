<?php declare(strict_types=1);

namespace DB\MiniArticleList;

use DB\MiniArticleList\MiniArticle;

class ArticleCollection {
   private $title;
   private $miniArticles;

   public static function fromValues(string $title, array $miniArticleValues) {
      $mArticles = [];
      foreach ($miniArticleValues as $maValues) {
         $mArticles[] = MiniArticle::fromValues($maValues);
      }
      return new self($title, $mArticles);
   }

   public function getTitle(): string {
      return $this->title;
   }

   public function getMiniArticles(): array {
      return $this->miniArticles;
   }

   public function getArticles($tagsAsOneString = false): array {
      $tagToArray = function($mArticle) use ($tagsAsOneString) {
         return $mArticle->toArray($tagsAsOneString);
      };
      return array_map($tagToArray, $this->miniArticles);
   }

   public function onPageid(int $pageid): bool {
      $maArticle = current($this->miniArticles);
      if ($maArticle) {
         return $maArticle->matchesPageid($pageid);
      }
      return false;
   }

   public function toArray(): array {
      return [
         'title' => $this->getTitle(),
         'articles' => $this->getArticles()
      ];
   }

   private function __construct(string $title, array $miniArticles) {
      $this->title = $title;
      $this->miniArticles = $miniArticles;
   }
}
