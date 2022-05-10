<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Packet\PacketTrait;

class HeadPacket {
   use PacketTrait;

   private const HTML_STYLE_SHEET_PATH = 'src/templates/css/page.css';
   private const JS_LOADER_PATH = 'src/templates/js/page.js';

   /**
    * @param $pageTitle - Text put in the meta "title" filed in the Head
    */
   public function __construct(string $pageTitle) {
      $this->setData('pageTitle', $pageTitle);
   }

   public function getStyleSheetPath(): string {
      return self::HTML_STYLE_SHEET_PATH;
   }

   public function getJavascriptPath(): string {
      return self::JS_LOADER_PATH;
   }
}
