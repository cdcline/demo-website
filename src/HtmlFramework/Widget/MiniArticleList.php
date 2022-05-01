<?php declare(strict_types=1);

namespace HtmlFramework\Widget;

use DB\MiniArticle;
use Utils\HtmlUtils;
use Utils\Parser;

class MiniArticleList {
   private $pageid;
   private $tags;
   private $miniArticleRows;

   public static function fromPageid(int $pageid): self {
      return new self($pageid);
   }

   /**
    * Generates something like
    * <div>
    *    <H3>Random Title<H3> --  https://github.com/cdcline/demo-website/issues/46
    *    <div> -- div with all possible tags
    *       <span>Tag 1</span>
    *       <span>Tag 2</span>
    *    </div>
    *    <div> -- div with sort orders
    *       <span>sortOrderA</span>
    *       <span>sortOrderB</span>
    *    </div>
    *    <div>
    *       <Mini Article Div> -- All the article mini info
    *    </div>
    * </div>
    */
   public function getHtml(): string {
      if (!$this->showMiniArticleRows()) {
         return '';
      }
      $maEls = [$this->getListHeaderHtml(), $this->getListTagsHtml(), $this->getSortHtml(), $this->getMiniArticleHtml()];
      return HtmlUtils::makeDivElement(implode(' ', $maEls), ['id' => 'mini-article-list']);
   }

   private function __construct(int $pageid) {
      $this->pageid = $pageid;
   }

   // I don't think I really want this in the end (the article text should suffice) but it's useful to see in dev
   private function getListHeaderHtml(): string {
      return HtmlUtils::makeH3Element('Mini Article List', 'fun');
   }

   /**
    * Should return something like
    * <div>
    *   <ul>
    *     <li>Tag 1</li>
    *     <li>Tag 2</li>
    *   </ul>
    * </div>
    */
   private function getListTagsHtml(): string {
      $tags = HtmlUtils::makeUnorderList($this->getTags(), /*addDataMeta*/true);
      return HtmlUtils::makeDivElement($tags, ['id' => 'mini-article-tag-list']);
   }

   /**
    * Should return a list of each articles
    * <div>
    *    <article 1>
    *    <article 2>
    * </div>

    * Each <article> should generate something like
    * <div>
    *    <h5>{$miniArticleTitle}</h5>
    *    <div>{$timeSpans}</div>
    *    <div>{$tagSpans}</div>
    *    <div>{$parsedArticle}</div>
    * <div>
    */
   private function getMiniArticleHtml(): string {
      $articleEls = [];
      $mArticleTextDivParams = ['class' => 'mini-article-text'];
      $mArticleContainerParams = ['class' => 'mini-article-container'];
      $mArticleListParams = ['id' => 'mini-article-entries'];
      foreach ($this->getMiniArticleRows() as $row) {
         $header = HtmlUtils::makeHXElement(5, $row['title']);
         $timeSpanDiv = $this->makeArticleDatesDiv((int)$row['start_date'], (int)$row['end_date']);
         $miniArticleDiv = HtmlUtils::makeDivElement(Parser::parseText($row['mini_article_text']), $mArticleTextDivParams);
         $miniArtileTagDiv = $this->makeMiniArticleTagDiv($row['tags']);
         $miniArticleInnerHtml = implode(' ', [$header, $timeSpanDiv, $miniArtileTagDiv, $miniArticleDiv]);
         $articleEls[] = HtmlUtils::makeDivElement($miniArticleInnerHtml, $mArticleContainerParams);
      }
      return HtmlUtils::makeDivElement(implode(' ', $articleEls), $mArticleListParams);
   }

   /**
    * Should generate somthing like:
    * <div>
    *    <span>Tag1</span>
    *    <span>Tag2</span>
    * </div>
    */
   private function makeMiniArticleTagDiv(array $tags) {
      $tagEls = [];
      $tagSpanParams = ['class' => 'mini-article-tags'];
      foreach ($tags as $tag) {
         $tagEls[] = HtmlUtils::makeSpanElement($tag, array_merge($tagSpanParams, ['data-value' => $tag]));
      }
      $tagHtml = implode(' ', $tagEls);
      $tagDivParams = ['class' => 'mini-article-tag-container'];
      return HtmlUtils::makeDivElement($tagHtml, $tagDivParams);
   }

   /**
    * Should generate somthing like:
    * <div>
    *    <span>Most Recent</span>|<span>Chronologically</span>
    * </div>
    */
   private function getSortHtml() {
      $mostRecentSpan = HtmlUtils::makeSpanElement('Most Recent', ['data-sort' => 'desc']);
      $leastRecentSpan = HtmlUtils::makeSpanElement('Chronologically', ['data-sort' => 'asc']);
      $sortHtml = implode(' | ', [$mostRecentSpan, $leastRecentSpan]);
      $sortDivParams = ['id' => 'mini-article-sort-container'];
      return HtmlUtils::makeDivElement($sortHtml, $sortDivParams);
   }

   /**
    * Should generate something like:
    * <div>
    *    <span {$data}>{$startDate}</span>
    *    <span {$data}>{$endDate}</span> -- if it exists
    * </div>
    */
   private function makeArticleDatesDiv(int $startDate, ?int $endDate) {
      $formatDate = function(int $timestamp): string {
         return date('m/d/y', $timestamp);
      };
      // Make the start date span
      $sDateParams = ['class' => 'ma-start-date', 'data-start-date' => $startDate];
      $sDateSpan = HtmlUtils::makeSpanElement($formatDate($startDate), $sDateParams);
      // Make the end date span (if it exists)
      $eDateSpan = '';
      if ($endDate) {
         $eDateParams = ['class' => 'ma-end-date', 'data-end-date' => $endDate];
         $eDateSpan = HtmlUtils::makeSpanElement($formatDate($endDate), $eDateParams);
      }
      // Stick the spans in a div
      $timeDivParams = ['class' => 'ma-date'];
      $innerTimeDivHtml = "{$sDateSpan}{$eDateSpan}";
      return HtmlUtils::makeDivElement($innerTimeDivHtml, $timeDivParams);
   }

   private function getTags(): array {
      if (isset($this->tags)) {
         return $tags;
      }
      $tags = [];
      foreach ($this->getMiniArticleRows() as $row) {
         $tags = array_merge($tags, $row['tags']);
      }
      return $this->tags = array_unique($tags);
   }

   private function showMiniArticleRows(): bool {
      return (bool)$this->getMiniArticleRows();
   }

   private function getMiniArticleRows(): array {
      if ($this->miniArticleRows) {
         return $this->miniArticleRows;
      }

      // For now we'll just grab all the data and filter on pageid here.
      return $this->miniArticleRows = array_filter(MiniArticle::fetchAll(), function ($row) {
         return $row['pageid'] == $this->pageid;
      });
   }
}
