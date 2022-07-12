<?php declare(strict_types=1);

namespace HtmlFramework\Widget;

use DB\PageIndex;
use HtmlFramework\Packet\ArticlePacket;
use HtmlFramework\Packet\WidgetCollectionPacket;
use HtmlFramework\Widget\CarouselController;
use HtmlFramework\Widget\MiniArticleList;

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
   // By "default" we'll add the MiniArticleList.
   private $defaultWidgets = [MiniArticleList::class, CarouselController::class];

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
      $getClassesFromDefault = function(array $includeWidgets = [], array $excludeWidgets = []): array {
         $defaultWidgets = $this->defaultWidgets;
         foreach ($excludeWidgets as $className) {
            if (($iWidget = array_search($className, $defaultWidgets)) !== false) {
               unset($defaultWidgets[$iWidget]);
            }
         }
         return array_merge($defaultWidgets, $includeWidgets);
      };

      switch ($this->wcPacket->getPageType()) {
         case PageIndex::WORK_TYPE:
            return $getClassesFromDefault();
         case PageIndex::HOMEPAGE_TYPE:
            return $getClassesFromDefault();
         default:
            return $getClassesFromDefault();
      }
   }
}
