<?php declare(strict_types=1);

namespace HtmlFramework\Widget;

use DB\MiniArticle;
use HtmlFramework\Packet\WidgetCollectionPacket;
use HtmlFramework\Widget\WidgetTrait;
use Utils\HtmlUtils;
use Utils\Parser;

class MiniArticleList {
   use WidgetTrait;

   private $tags;
   private $miniArticleRows;

   /**
    * Generates something like
    * <div id="mini-article-list">
    *    <div id="ma-head-container">
    *      <div id="ma-header-and-sort-container">
    *        <H3>Random Title<H3> --  https://github.com/cdcline/demo-website/issues/46
    *        {$maSortDiv} -- div with sort orders
    *      </div>
    *      {$maTagsDiv} -- div with all possible tags
    *      {$maEntriesDiv} -- div the mini article entries
    *    </div>
    * </div>
    */
   protected function getHtml(): string {
      if (!$this->renderWidget()) {
         return '';
      }

      $maHeaderAndSort = [$this->getListHeaderHtml(), $this->getSortHtml()];
      $maHSContainer = HtmlUtils::makeDivElement(implode(' ', $maHeaderAndSort), ['id' => 'ma-header-and-sort-container']);
      $maHeadEls = [$maHSContainer, $this->getListTagsHtml()];
      $maHeadContainer = HtmlUtils::makeDivElement(implode(' ', $maHeadEls), ['id' => 'ma-head-container']);
      $maEls = [$maHeadContainer, $this->getMiniArticleHtml()];
      return HtmlUtils::makeDivElement(implode(' ', $maEls), ['id' => 'mini-article-list']);
   }

   // I don't think I really want this in the end (the article text should suffice) but it's useful to see in dev
   private function getListHeaderHtml(): string {
      return HtmlUtils::makeH3Element('Mini Article List', 'fun');
   }

   /**
    * Should return something like: {$maTagsDiv}
    * <div id="mini-article-tag-list">
    *   <span class="ma-tag" data-value="Tag 1">Tag 1</span>
    *   <span class="ma-tag" data-value="Tag 2">Tag 2</span>
    *   ... -- for each tag
    * </div>
    */
   private function getListTagsHtml(): string {
      $tagSpans = [];
      $commonTagElements = ['class' => 'ma-tag'];
      foreach($this->getTags() as $tag) {
         $tagElements = array_merge($commonTagElements, ['data-value' => $tag]);
         $tagSpans[] = HtmlUtils::makeSpanElement($tag, $tagElements);
      }
      return HtmlUtils::makeDivElement(implode(' ', $tagSpans), ['id' => 'mini-article-tag-list']);
   }

   /**
    * The sorting options at the top of the Mini Article List: {$maSortDiv}
    *
    * Should generate somthing like:
    * <div id="mini-article-sort-container">
    *    <span data-sort="desc">Most Recent</span>
    *    |
    *    <span data-sort="asc">Chronologically</span>
    * </div>
    */
   private function getSortHtml(): string {
      $mostRecentSpan = HtmlUtils::makeSpanElement('Most Recent', ['data-sort' => 'desc']);
      $leastRecentSpan = HtmlUtils::makeSpanElement('Chronologically', ['data-sort' => 'asc']);
      $sortHtml = implode(' | ', [$mostRecentSpan, $leastRecentSpan]);
      $sortDivParams = ['id' => 'mini-article-sort-container'];
      return HtmlUtils::makeDivElement($sortHtml, $sortDivParams);
   }

   /**
    * Should return a list of each articles: {$maEntriesDiv}
    * <div id="mini-article-entries>
    *    {$articleEntryDiv1}
    *    {$articleEntryDiv2}
    *    ...
    * </div>

    * Each {$articleEntryDiv} should generate something like
    * <div class="ma-entry-container">
    *    <div class="ma-entry-head-container">
    *      <div class="ma-entry-title-container">
    *          <h5>{$miniArticleTitle}</h5>
    *          {$entryTimeDiv}
    *      </div>
    *      {$entryTagDiv}
    *    </div>
    *    <div class="ma-entry-text-container">{$parsedArticle}</div>
    * <div>
    */
   private function getMiniArticleHtml(): string {
      $articleEls = [];
      $mArticleListParams = ['id' => 'mini-article-entries'];
      $maEntryContainerParams = ['class' => 'ma-entry-container'];
      $maEntryHeadContainerParams = ['class' => 'ma-entry-head-container'];
      $maEntryTitleContainerParams = ['class' => 'ma-entry-title-container'];
      $maEntryArticleTextContainerParams = ['class' => 'ma-entry-text-container'];

      $makeTitleContainer = function(string $title, int $startDate, int $endDate) use ($maEntryTitleContainerParams): string {
         $header = "<div>". HtmlUtils::makeHXElement(5, $title).'</div>';
         $timeSpanDiv = $this->makeArticleDatesDiv($startDate, $endDate);
         return HtmlUtils::makeDivElement(implode(' ', [$header, $timeSpanDiv]), $maEntryTitleContainerParams);
      };

      foreach ($this->getMiniArticleRows() as $row) {
         $titleContainer = $makeTitleContainer($row['title'], (int)$row['start_date'], (int)$row['end_date']);
         $headContainer = HtmlUtils::makeDivElement(implode(' ', [$titleContainer, $this->makeMiniArticleTagDiv($row['tags'])]), $maEntryHeadContainerParams);
         $maTextContainer = HtmlUtils::makeDivElement(Parser::parseText($row['mini_article_text']), $maEntryArticleTextContainerParams);
         $articleEls[] = HtmlUtils::makeDivElement("{$headContainer} {$maTextContainer}", $maEntryContainerParams);
      }

      return HtmlUtils::makeDivElement(implode(' ', $articleEls), $mArticleListParams);
   }

   /**
    * The dates shown for each mini article entry: {$entryTimeDiv}
    *
    * Should generate something like:
    * <div class="ma-entry-date-container">
    *    <span class="ma-entry-date ma-start-date-container">Started:
    *       <span class="ma-start-date" data-start-date>{$startDate}</span>
    *    </span>
    *    <span class="ma-entry-date ma-end-date-container">Ended: -- if it exists
    *       <span class="ma-end-date" data-end-date {$data}>{$endDate}</span>
    *    </span>
    * </div>
    */
   private function makeArticleDatesDiv(int $startDate, ?int $endDate): string {
      // We want to view the dates in a reasonable format. We'll show by year
      $formatDate = function(int $timestamp): string {
         return date('n/Y', $timestamp);
      };
      // We need a common class to hook css on
      $entryDateTxtClass = ['ma-text-date-container'];
      // Create the internal "start date" span with timestamp values
      $sDateParams = ['class' => 'ma-start-date', 'data-start-date' => $startDate];
      $sDateSpan = HtmlUtils::makeSpanElement($formatDate($startDate), $sDateParams);
      // Add "start date" text
      $sTextInnerHtml = "{$sDateSpan}";
      // Add the common class to the specific class
      $sTextClasses = implode(' ', array_merge($entryDateTxtClass, ['ma-start-date-container']));
      $sTextParams = ['class' => $sTextClasses];
      $sTextSpan = HtmlUtils::makeSpanElement($sTextInnerHtml, $sTextParams);
      // Make the end date span (if it exists)
      $eTextSpan = '';
      if ($endDate) {
         $eDateParams = ['class' => 'ma-end-date', 'data-end-date' => $endDate];
         $eDateSpan = ' - ' . HtmlUtils::makeSpanElement("{$formatDate($endDate)}", $eDateParams);
         // Add the common class to the specific clas
         $eTextClasses = implode(' ', array_merge($entryDateTxtClass, ['ma-end-date-container']));
         $eTextParams = ['class' => $eTextClasses];
         $eTextSpan = HtmlUtils::makeSpanElement($eDateSpan, $eTextParams);
      }
      // Stick the spans in a div
      $timeDivParams = ['class' => 'ma-entry-date-container'];
      $innerTimeDivHtml = "{$sTextSpan}{$eTextSpan}";
      return HtmlUtils::makeDivElement($innerTimeDivHtml, $timeDivParams);
   }

   /**
    * The list of tags associated with a specific mini article entry: {$entryTagDiv}
    *
    * Should generate somthing like:
    * <div class="ma-entry-tag-container">
    *    <span class="ma-tag ma-entry-tags" data-value="Tag 1">Tag1</span>
    *    <span class="ma tag ma-entry-tags" data-value="Tag 2">Tag2</span>
    *    ... - foreach tag
    * </div>
    */
   private function makeMiniArticleTagDiv(array $tags): string {
      $tagEls = [];
      $tagSpanParams = ['class' => 'ma-tag ma-entry-tags'];
      foreach ($tags as $tag) {
         $tagEls[] = HtmlUtils::makeSpanElement($tag, array_merge($tagSpanParams, ['data-value' => $tag]));
      }
      $tagHtml = implode(' ', $tagEls);
      return HtmlUtils::makeDivElement($tagHtml, ['class' => 'ma-entry-tag-container']);
   }

   /**
    * @returns array - Unique list of tags found in the article rows
    */
   private function getTags(): array {
      if (isset($this->tags)) {
         return $tags;
      }
      $tags = [];
      foreach ($this->getMiniArticleRows() as $row) {
         $tags = array_merge($tags, $row['tags']);
      }
      $uTags = array_unique($tags);
      sort($uTags, SORT_STRING);
      return $this->tags = $uTags;
   }

   protected function renderWidget(): bool {
      return (bool)$this->getMiniArticleRows();
   }

   private function getMiniArticleRows(): array {
      if ($this->miniArticleRows) {
         return $this->miniArticleRows;
      }

      // For now we'll just grab all the data and filter on pageid here.
      return $this->miniArticleRows = array_filter(MiniArticle::fetchAll(), function ($row) {
         return $row['pageid'] == $this->wcPacket->getPageid();
      });
   }
}
