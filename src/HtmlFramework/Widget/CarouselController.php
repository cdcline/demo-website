<?php declare(strict_types=1);

namespace HtmlFramework\Widget;

use DB\PageHeaderImages;
use Exception;
use HtmlFramework\Widget\WidgetTrait;
use Utils\HtmlUtils;
use Utils\LoremIpsumUtils;

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
    * {$prevOptionHtml}
    * <div class="controller-option-prev-container">
    *    <img class="js-prev-button" />
    * </div>
    */
   private function getPrevOptionHtml(): string {
      $imgHtml = '<p class="js-prev-button">Prev</p>';
      return HtmlUtils::makeDivElement($imgHtml, ['class' => 'controller-option-prev-container']);
   }

   /*
    * {$setOptionHtml}
    * <div class="controller-option-set-container">
    *    {repeat}
    *       <div class="controller-option-set-btn" data-position={$i}>&nbsp;</div>
    *    {/repeat}
    * </div>
    */
   private function getSetOptionHtml(): string {
      $slides = $this->getPageHeaderImages()->toFullArray();
      $position = 1;
      $generateBtnHtml = function($slideData) use (&$position): string {
         $position = $slideData['orderby'] ?? $position;
         $setValues = [
            'data-position' => $position++,
            'class' => 'js-set-carousel-slide controller-option-set-btn'
         ];
         return HtmlUtils::makeDivElement('&nbsp', $setValues);
      };
      $btnsHtml = implode(' ', array_map($generateBtnHtml, $slides));
      return HtmlUtils::makeDivElement($btnsHtml, ['class' => 'controller-option-set-container']);
   }

   /*
    * {$nextOptionHtml}
    * <div class="controller-option-next-container">
    *    <img class="js-next-button" />
    * </div>
    */
   private function getNextOptionHtml(): string {
      $imgHtml = '<p class="js-next-button">Next</p>';
      return HtmlUtils::makeDivElement($imgHtml, ['class' => 'controller-option-next-container']);
   }
}
