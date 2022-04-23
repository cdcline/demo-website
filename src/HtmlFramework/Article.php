<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;

/**
 * The "Article" is the section of the html that changes the most
 * when the user navigates to a separate url.
 *
 * Most people would think of this as the "web page" but there are
 * a lot of pieces surrounding it.
 */

class Article extends HtmlElement {
   private $articlePath;
   private const FRAMEWORK_FILE = 'article.phtml';

   /**
    * @param string $articlePath - Path to the phtml file we want to print
    *
    * NOTE: We'll probably want the page data passed in here too but there
    *       isn't a use for it yet with just static values
    */
   public function __construct(string $articlePath) {
      $this->articlePath = $articlePath;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function getPathToArticle(): string {
      return $this->articlePath;
   }
}
