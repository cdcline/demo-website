<?php declare(strict_types=1);

namespace HtmlFramework\Widget;

use Utils\HtmlUtils;
use HtmlFramework\Widget\WidgetTrait;

class BlockOFun {
   use WidgetTrait;

   const FUN_IMAGE_WIDTH = 140;
   const FUN_IMAGE_HEIGHT = 140;
   const FUN_CLASS = 'block-o-fun';

   public function getHtml(): string {
      $funElements = [];
      $funElements[] = HtmlUtils::makeH1Element('This is your basic block of Fun!', 'fun');
      $funElements[] = $this->makeImageElement();
      for ($i = 1; $i <= 4; $i++) {
         $funElements[] = HtmlUtils::addRandomFun($this->getText($i), rand(0, 100));
         $funElements[] = HtmlUtils::makePageWhitespace();
      }
      $fpHtml = HtmlUtils::makePElement(implode(' ', $funElements));
      return HtmlUtils::makeDivElement($fpHtml, ['class' => 'block-o-fun']);
   }

   protected function renderWidget(): bool {
      return true;
   }

   private function makeImageElement(): string {
      $picsumPhoto = HtmlUtils::getPicsumPhoto(self::FUN_IMAGE_WIDTH, self::FUN_IMAGE_HEIGHT);
      $imgAttributes = [
         'id' => 'fun-button',
         'src' => $picsumPhoto,
         'width' => self::FUN_IMAGE_WIDTH,
         'height' => self::FUN_IMAGE_HEIGHT,
         'alt' => 'random image'
      ];
      return HtmlUtils::makeImageElement($imgAttributes);
   }

   private function getText(int $funIndex): string {
      switch ($funIndex) {
         case 1: return <<<EOT
Fermentum dui faucibus in ornare quam. Ipsum faucibus vitae aliquet nec ullamcorper sit amet risus nullam. Ut lectus arcu bibendum at. Posuere lorem ipsum dolor sit. Ultrices mi tempus imperdiet nulla malesuada. In aliquam sem fringilla ut morbi tincidunt augue interdum. Consequat semper viverra nam libero justo laoreet.
EOT;
         case 2: return <<<EOT
Vitae tempus quam pellentesque nec nam aliquam sem et. A erat nam at lectus urna duis convallis convallis tellus. Nullam non nisi est sit amet. Augue lacus viverra vitae congue eu. Id diam vel quam elementum pulvinar etiam non quam. Sed viverra ipsum nunc aliquet bibendum enim facilisis gravida. In iaculis nunc sed augue lacus viverra vitae. A erat nam at lectus urna duis convallis convallis tellus.
EOT;
         case 3: return <<<EOT
Curabitur pulvinar, nisl non scelerisque varius, lectus orci faucibus ante, quis condimentum quam elit vitae purus. Etiam vitae purus lacinia, convallis leo at, commodo justo. Duis sed aliquet est. Vestibulum semper justo porttitor metus ullamcorper, eu bibendum enim convallis. Nunc hendrerit venenatis libero fringilla facilisis. Duis vitae enim tristique, gravida risus at, consequat ipsum. Suspendisse vitae ipsum vel justo euismod blandit. Praesent at mollis eros, vel finibus augue. Cras lacus turpis, porttitor sed purus quis, vulputate iaculis metus. In efficitur, est ac dapibus consectetur, metus neque viverra nibh, nec aliquam nunc eros non urna. Vivamus venenatis dapibus tempor. Cras sollicitudin ante enim, sit amet pharetra ligula facilisis id. Sed tristique hendrerit mauris, at tempus lacus iaculis in. Mauris viverra vel arcu vel pellentesque.
EOT;
         case 4: return <<<EOT
Donec lobortis enim quis dui auctor, et interdum purus tincidunt. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed quam massa, fringilla vitae lorem aliquet, vestibulum elementum ante. Vivamus sodales metus in porta hendrerit. Phasellus id cursus orci, eu dictum eros. Nulla vitae sem nec libero placerat gravida. Pellentesque scelerisque suscipit velit et convallis. Curabitur vulputate tincidunt enim, eu consectetur ipsum pretium ac.
EOT;
         default: return <<<EOT
Oops. Too far!
EOT;
      }
   }
}
