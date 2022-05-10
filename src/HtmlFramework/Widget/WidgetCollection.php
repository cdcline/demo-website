<?php declare(strict_types=1);

namespace HtmlFramework\Widget;

use HtmlFramework\Packet\ArticlePacket;
use HtmlFramework\Packet\WidgetCollectionPacket;
use HtmlFramework\Widget\BlockOFun;
use HtmlFramework\Widget\MiniArticleList;
use DB\PageIndex;

/**
 * Each "Page Type" can have an arbitrary number of "Widgets" linked to that type.
 *
 * This:
 *  - Figures out what widgets to display
 *  - Sending data to the Widgets
 *  - Returns all the aggregated widget HTML
 */
class WidgetCollection {
   private $wcPacket;
   // By "default" we'll add all the widgets.
   private $defaultWidgets = [MiniArticleList::class, BlockOFun::class];

   public static function getHtmlFromArticlePacket(ArticlePacket $aPacket) {
      $wcPacket = WidgetCollectionPacket::fromValues($aPacket->getPageType(), $aPacket->getPageid());
      return (new self($wcPacket))->getAllWidgetHtml();
   }

   private function __construct(WidgetCollectionPacket $wcPacket) {
      $this->wcPacket = $wcPacket;
   }

   private function getAllWidgetHtml(): string {
      $wHtml = [];
      foreach ($this->getWidgetClasses() as $wClassStr) {
         $wHtml[] = $wClassStr::getHtmlFromWidgetCollectionPacket($this->wcPacket);
      }
      return implode(' ', $wHtml);
   }

   /**
    * Not quite sure if it's better to link class -> [widgets] here or in the
    * Page logic but I think when it's clearer here for now.
    *
    * NOTE: This is probably where I'd re-order things if needed
    */
   private function getWidgetClasses() {
      $getClassesFromDefault = function(array $excludeFromDefault = []): array {
         $defaultWidgets = $this->defaultWidgets;
         foreach ($excludeFromDefault as $className) {
            if (($iWidget = array_search($className, $defaultWidgets)) !== false) {
               unset($defaultWidgets[$iWidget]);
            }
         }
         return $defaultWidgets;
      };

      switch ($this->wcPacket->getPageType()) {
         case PageIndex::DEV_TYPE:
            // There's enough with the Mini Article List data on the dev page
            return $getClassesFromDefault([BlockOFun::class]);
         case PageIndex::HOMEPAGE_TYPE:
            // We add the Block-O-Fun back into the template to show that you can do that
            return $getClassesFromDefault([BlockOFun::class]);
         default:
            return $getClassesFromDefault();
      }
   }
}
