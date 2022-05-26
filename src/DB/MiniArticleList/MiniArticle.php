<?php declare(strict_types=1);

namespace DB\MiniArticleList;

class MiniArticle {
   private $pageid;
   private $title;
   private $text;
   private $startDate;
   private $endDate;
   private $tags;

   public static function fromValues(array $values): self {
      return new self(
         $values['title'],
         $values['text'],
         (int)$values['start_date'],
         (int)$values['end_date'],
         $values['tags'] ?? [],
         (int)$values['pageid']
      );
   }

   private function __construct(string $title, string $text, int $startDate, int $endDate, array $tags, int $pageid) {
      $this->title = $title;
      $this->text = $text;
      $this->startDate = $startDate;
      $this->endDate = $endDate;
      $this->tags = $tags;
      $this->pageid = $pageid;
   }

   public function matchesPageid(int $pageid): bool {
      return $this->pageid == $pageid;
   }

   public function toArray() {
      return [
         'pageid' => $this->pageid,
         'title' => $this->title,
         'text' => $this->text,
         'start_date' => $this->startDate,
         'end_date' => $this->endDate,
         'tags' => $this->tags
      ];
   }
}
