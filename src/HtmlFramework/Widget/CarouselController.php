<?php declare(strict_types=1);

namespace HtmlFramework\Widget;

use DB\PageHeaderImages;
use Exception;
use HtmlFramework\Widget\WidgetTrait;
use Utils\HtmlUtils;

/**
 * We have a carousel in the header that cycles through slides but we'd also
 * like the ability to view a specific image.
 *
 * This widget creates a block of buttons that allows the user to click on one
 * to view a specific slide.
 *
 * There should be only 1 per page.
 *
 * Basic structure:
 * <carousel controller>
 *    <carousel options>
 *       <prev button />
 *       <set buttons />
 *       <next button />
 *    </>
 * </>
 */
class CarouselController {
   use WidgetTrait;

   public static function getHtmlForTemplate(): string {
      throw new Exception('Widget not supported in template');
   }

   protected function renderWidget(): bool {
      return $this->getPageHeaderImages()->isCarousel();
   }

   /**
    * <div id="carousel-controller">
    *    <div class="controller-option-container">
    *       {$prevOptionHtml}
    *       {$setOptionHtml}
    *       {$nextOptionHtl}
    *    </div>
    * </div>
    */
   protected function getHtml(): string {
      if (!$this->renderWidget()) {
         return '';
      }

      $optionElements = [
         $this->getPrevOptionHtml(),
         $this->getSetOptionHtml(),
         $this->getNextOptionHtml()
      ];
      $optionContainerHtml = HtmlUtils::makeDivElement(implode(' ', $optionElements), ['class' => 'controller-option-container']);
      return HtmlUtils::makeDivElement($optionContainerHtml, ['id' => 'carousel-controller']);
   }

   private function getPageHeaderImages(): PageHeaderImages {
      if (isset($this->pageHeaderImages)) {
         return $this->pageHeaderImages;
      }

      return $this->pageHeaderImages = PageHeaderImages::fromPageid($this->wcPacket->getPageid());
   }

   /*
    * <div class="controller-option-prev-container">
    *   <svg left-arrow />
    * </div>
    */
   private function getPrevOptionHtml(): string {
      $options =['class' => 'js-prev-button controller-option-prev-container'];
      return HtmlUtils::makeDivElement(self::getSvgArrow(/*next*/false), $options);
   }


   /*
    * <div class="controller-option-set-container">
    *    {repeat}
    *       <div class="controller-option-set-btn" data-position={$i}>&nbsp;</div>
    *    {/repeat}
    * </div>
    */
   private function getSetOptionHtml(): string {
      $slides = $this->getPageHeaderImages()->toFullArray();
      $position = 1;
      $classes = ['js-set-carousel-slide', 'controller-option-set-btn'];
      $generateBtnHtml = function($slideData) use (&$position, $classes): string {
         $position = $slideData['orderby'] ?? $position;
         $sClasses = $position === 1 ? array_merge($classes, ['active']) : $classes;
         $setValues = [
            'data-position' => $position++,
            'class' => implode(' ', $sClasses)
         ];
         return HtmlUtils::makeDivElement('&nbsp', $setValues);
      };
      $btnsHtml = implode(' ', array_map($generateBtnHtml, $slides));
      return HtmlUtils::makeDivElement($btnsHtml, ['class' => 'controller-option-set-container']);
   }

   /*
    * <div class="js-next-button controller-option-next-container">
    *    <svg right-arrow />
    * </div>
    */
   private function getNextOptionHtml(): string {
      $options =['class' => 'js-next-button controller-option-next-container'];
      return HtmlUtils::makeDivElement(self::getSvgArrow(/*next*/true), $options);
   }

   private function getSvgArrow(bool $next): string {
      $height = $width = 30;
      $points = $next ? "0,0 0,30 30,15" : "0,15 30,0 30,30";
      $class = "carousel-controller-arrow";
      return <<<EOT
<svg height="{$height}" width="{$width}">
   <polygon class="{$class}" points="{$points}" />
</svg>
EOT;
   }
}
