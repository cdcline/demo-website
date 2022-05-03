<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Packet\ArticlePacket;
use HtmlFramework\Widget\MiniArticleList;
use HtmlFramework\Widget\WidgetCollection;
use Utils\Parser;

/**
 * The "Article" is the section of the html that changes the most
 * when the user navigates to a separate url.
 *
 * Most people would think of this as the "web page" but there are
 * a lot of pieces surrounding it.
 */

class Article extends HtmlElement {
   private const FRAMEWORK_FILE = 'article.phtml';

   public static function fromValues(string $pageType, int $pageid, string $articlePath, array $pageData, string $mainArticle): self {
      $packet = new ArticlePacket($pageType, $pageid, $articlePath, $pageData, $mainArticle);
      return new self($packet);
   }

   private function __construct(ArticlePacket $packet) {
      $this->packet = $packet;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function getParsedMainArticle(): string {
      return Parser::parseText($this->packet->getData('mainArticle'));
   }

   protected function getWidgetCollectionHtml(): string {
      return WidgetCollection::getHtml($this->packet);
   }

   protected function getData(string $index) {
      $articleData = $this->packet->getData('articleData');
      return isset($articleData[$index]) ? $articleData[$index] : null;
   }
}
