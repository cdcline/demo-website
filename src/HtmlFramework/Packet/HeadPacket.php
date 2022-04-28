<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Packet\PacketTrait;

class HeadPacket {
   use PacketTrait;

   private const HTML_STYLE_SHEET_PATH = 'src/templates/css/page.css';

   /**
    * @param $pageTitle - Text put in the meta "title" filed in the Head
    */
   public function __construct(string $pageTitle) {
      $this->setData('pageTitle', $pageTitle);
   }

   public function getStyleSheetPath(): string {
      return self::HTML_STYLE_SHEET_PATH;
   }
}
