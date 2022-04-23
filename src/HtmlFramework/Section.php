<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Nav as PageNav;
use HtmlFramework\Article as PageArticle;

/**
 * The "section" houses the nav and acticle and allows the layout
 * to look good on smaller screen using css.
 */
class Section extends HtmlElement {
   private const FRAMEWORK_FILE = 'section.phtml';

   public function __construct(PageNav $nav, PageArticle $article) {
      $this->nav = $nav;
      $this->article = $article;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }
}
