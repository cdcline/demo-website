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
   private $pageData;
   private const FRAMEWORK_FILE = 'article.phtml';

   /**
    * @param string $articlePath - Path to the phtml file we want to print
    * @param array $pageData - Should have the data we want to display
    */
   public function __construct(string $articlePath, array $pageData) {
      $this->pageData = $pageData;
      $this->articlePath = $articlePath;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function getPathToArticle(): string {
      return $this->articlePath;
   }

   protected function getPageData(string $index) {
      return isset($this->pageData[$index]) ? $this->pageData[$index] : 'fail';
   }
}
