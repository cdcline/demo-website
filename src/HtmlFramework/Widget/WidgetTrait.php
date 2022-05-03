<?php declare(strict_types=1);

namespace HtmlFramework\Widget;

use HtmlFramework\Packet\ArticlePacket;

/**
 * This file is most here to talk about what a "Widget" is for this code base.
 *
 * We're gonna use a "widget" as a way of creating a complicated html structure
 * with fancy behaviors.
 *
 * If we wanted to:
 *  - Make a fancy list of things then filter and order them: https://github.com/cdcline/demo-website/issues/26
 *  - Create a slideshow: https://github.com/cdcline/demo-website/issues/36
 *  - Add a login panel: https://github.com/cdcline/demo-website/issues/33
 *
 * These would be widgets because each piece would be unique html, js and css
 * that may or may not be wanted on the page.
 *
 * If we want to just display some formated text with pictures and some headers,
 * that's what the `page_index.main_article` is for.
 */
trait WidgetTrait {
   // This adds some odd complexity but it's an interesting direction. It could
   // be annoying to keep `getHtml` public in the future but for now it allows
   // a lot of variation
   abstract public static function getHtmlFromArticlePacket(ArticlePacket $aPacket): string;
   // It's gonna output some crazy html thing that the JS and CSS are tightly coupled with
   abstract public function getHtml(): string;
   // Sometimes we're gonna wanna skip all the render logic so we'll make a common option for that here.
   protected function renderWidget(): bool {
      return true;
   };
}
