<?php declare(strict_types=1);

namespace HtmlFramework;

use HtmlFramework\Element as HtmlElement;
use HtmlFramework\Footer as PageFooter;
use HtmlFramework\Header as PageHeader;
use HtmlFramework\Section as PageSection;

/**
 * The "Body" is the section of html that holds most of the stuff the user will
 * see.
 *
 * It's basically everything but the "head" element used by browsers for browser
 * things.
 */

class Body extends HtmlElement {
   protected $header;
   protected $section;
   protected $footer;
   private const FRAMEWORK_FILE = 'body.phtml';

   public function __construct(PageHeader $header, PageSection $section, PageFooter $footer) {
      $this->header = $header;
      $this->section = $section;
      $this->footer = $footer;
   }

   protected function getFrameworkFile(): string {
      return self::FRAMEWORK_FILE;
   }

   protected function getJavascriptPath(): string {
      return 'src/templates/js/page.js';
   }
}
