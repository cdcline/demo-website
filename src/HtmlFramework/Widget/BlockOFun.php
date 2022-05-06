<?php declare(strict_types=1);

namespace HtmlFramework\Widget;

use Utils\HtmlUtils;
use Utils\LoremIpsumUtils;
use HtmlFramework\Packet\ArticlePacket;
use HtmlFramework\Widget\WidgetTrait;

/**
 * Your basic block'o'fun to play with
 *
 * Creates something like
 * <div class="block-o-fun">
 *    <p>
 *       <h1 class="fun">
 *       <img class="fun-button">
 *       {repeat}
 *          {$block of text with random fun}
 *          <br><br>
 *       {/repeat}
 *    </p>
 * </div>
 */
class BlockOFun {
   use WidgetTrait;

   const FUN_BLOCKS = 3;
   const FUN_IMAGE_WIDTH = 140;
   const FUN_IMAGE_HEIGHT = 140;
   const FUN_CLASS = 'fun';
   const FUN_IMG_CLASS = 'fun-btn';
   const FUN_BLOCK_CLASS = 'block-o-fun';
   const FUN_HEADER = 'This is your basic block of Fun!';

   public static function getHtmlForTemplate(): string {
      throw new Exception('Widget not supported in template');
   }

   protected function getHtml(): string {
      $funElements = [];
      $funElements[] = HtmlUtils::makeH1Element(self::FUN_HEADER, self::FUN_CLASS);
      $funElements[] = $this->makeImageElement();
      for ($i = 1; $i <= self::FUN_BLOCKS; $i++) {
         $randomSpanText = HtmlUtils::addRandomFun($this->getText(), rand(0, 100));
         $funElements[] = HtmlUtils::makePElement($randomSpanText);
      }
      return HtmlUtils::makeDivElement(implode(' ', $funElements), ['class' => self::FUN_BLOCK_CLASS]);
   }

   private function makeImageElement(): string {
      $picsumPhoto = HtmlUtils::getPicsumPhoto(self::FUN_IMAGE_WIDTH, self::FUN_IMAGE_HEIGHT);
      $imgAttributes = [
         'class' => self::FUN_IMG_CLASS,
         'src' => $picsumPhoto,
         'width' => self::FUN_IMAGE_WIDTH,
         'height' => self::FUN_IMAGE_HEIGHT,
         'alt' => 'random image'
      ];
      return HtmlUtils::makeImageElement($imgAttributes);
   }

   private function getText(): string {
      return LoremIpsumUtils::getRandomParagraph();
   }
}
