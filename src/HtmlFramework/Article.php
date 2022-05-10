<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Packet\ArticlePacket;
use HtmlFramework\Packet\WidgetCollectionPacket;
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

   public static function fromValues(string $pageType, int $pageid, string $templateForPageType, array $dataForTypeTemplate, string $mainArticle): self {
      $wcPacket = WidgetCollectionPacket::fromValues($pageType, $pageid);
      $packet = new ArticlePacket($wcPacket, $templateForPageType, $dataForTypeTemplate, $mainArticle);
      return new self($packet);
   }

   public function getWidgetCollectionPacket(): WidgetCollectionPacket {
      return $this->packet->getWidgetCollectionPacket();
   }

   private function __construct(ArticlePacket $packet) {
      $this->packet = $packet;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function getPageType(): string {
      return $this->packet->getPageType();
   }

   protected function getParsedMainArticle(): string {
      return Parser::parseText($this->packet->getData('mainArticle'));
   }

   protected function getHtmlForPageType(): string {
      // Probably a better way of doing this; basically we render the template between the ob calls
      ob_start();
      require $this->packet->getData('templateForPageType');
      return ob_get_clean();
   }

   protected function getWidgetCollectionHtml(): string {
      return WidgetCollection::getHtmlFromArticlePacket($this->packet);
   }

   /**
    * We're gonna try to isolate the data we actually use in the "type template"
    * through this abstraction.
    *
    * It's useful to know what data is specifically needed and also useful to have
    * it in a single spot.
    */
   protected function getData(string $index) {
      $articleData = $this->packet->getData('dataForTypeTemplate');
      return isset($articleData[$index]) ? $articleData[$index] : null;
   }
}
