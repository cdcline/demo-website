<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Packet\ArticlePacket;
use Parsedown;

/**
 * The "Article" is the section of the html that changes the most
 * when the user navigates to a separate url.
 *
 * Most people would think of this as the "web page" but there are
 * a lot of pieces surrounding it.
 */

class Article extends HtmlElement {
   private const FRAMEWORK_FILE = 'article.phtml';

   public static function fromValues(string $articlePath, array $pageData, string $mainArticle): self {
      $packet = new ArticlePacket($articlePath, $pageData, $mainArticle);
      return new self($packet);
   }

   private function __construct(ArticlePacket $packet) {
      $this->packet = $packet;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function getParsedMainArticle(): string {
      $parser = new Parsedown();
      // Sanatizes the output (in theory)
      $parser->setSafeMode(true);
      return $parser->text($this->packet->getData('mainArticle'));
   }

   protected function getData(string $index) {
      $articleData = $this->packet->getData('articleData');
      return isset($articleData[$index]) ? $articleData[$index] : null;
   }
}
