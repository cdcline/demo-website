<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use DB\PageIndex;
use HtmlFramework\Packet\PacketTrait;

class HeadPacket {
   use PacketTrait;

   private const ORANGE_THEME_STYLE_SHEET_PATH = 'src/templates/css/orange.css';
   private const GREY_THEME_STYLE_SHEET_PATH = 'src/templates/css/grey.css';
   private const GREEN_THEME_STYLE_SHEET_PATH = 'src/templates/css/green.css';
   private const BLACK_THEME_STYLE_SHEET_PATH = 'src/templates/css/black.css';
   private const JS_LOADER_PATH = 'src/templates/js/page.js';
   private const FAVICON_PATH = '/src/images/fav/favicon.ico';
   private const FAVICON_PATH_16 = '/src/images/fav/favicon-16x16.png';
   private const FAVICON_PATH_32 = '/src/images/fav/favicon-32x32.png';
   private const FAVICON_PATH_192 = '/src/images/fav/android-chrome-192x192.png';
   private const FAVICON_PATH_512 = '/src/images/fav/android-chrome-512x512.png';
   private const APPLE_TOUCH = '/src/images/fav/apple-touch-icon.png';
   private const MANIFEST_PATH = '/src/images/fav/site.webmanifest';

   private $pageTheme;

   /**
    * @param $pageTitle - Text put in the meta "title" filed in the Head
    */
   public function __construct(string $pageTheme, string $pageTitle) {
      $this->pageTheme = $pageTheme;
      $this->setData('pageTitle', $pageTitle);
   }

   public function getStyleSheetPath(): string {
      switch ($this->pageTheme) {
         case PageIndex::ORANGE_THEME:
            return self::ORANGE_THEME_STYLE_SHEET_PATH;
         case PageIndex::GREY_THEME:
            return self::GREY_THEME_STYLE_SHEET_PATH;
         case PageIndex::GREEN_THEME:
            return self::GREEN_THEME_STYLE_SHEET_PATH;
         case PageIndex::BLACK_THEME:
            return self::BLACK_THEME_STYLE_SHEET_PATH;
      }
      return self::ORANGE_THEME_STYLE_SHEET_PATH;
   }

   public function getJavascriptPath(): string {
      return self::JS_LOADER_PATH;
   }

   public function getFavPath(string $type = '') {
      switch($type) {
         case '16':
            return self::FAVICON_PATH_16;
         case '32':
            return self::FAVICON_PATH_32;
         case '192':
            return self::FAVICON_PATH_192;
         case '512':
            return self::FAVICON_PATH_512;
         case 'manifest':
            return self::MANIFEST_PATH;
         case 'apple':
            return self::APPLE_TOUCH;
         default:
            return self::FAVICON_PATH;
      }
   }
}
