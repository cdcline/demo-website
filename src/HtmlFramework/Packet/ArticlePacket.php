<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Packet\PacketTrait;
use HtmlFramework\Packet\WidgetCollectionPacket;

class ArticlePacket {
   use PacketTrait;

   private $wcPacket;

   /**
    * @param string $articlePath - Path to the phtml file we want to print
    * @param array $pageData - Should only have the data we want to display in the article
    * @param array $mainArticle - The main text that will be parsed into html and displayed on the page
    */
   public function __construct(WidgetCollectionPacket $wcPacket, string $articlePath, array $articleData, string $mainArticle) {
      $this->wcPacket = $wcPacket;
      $this->setData('articlePath', $articlePath);
      $this->setData('articleData', $articleData);
      $this->setData('mainArticle', $mainArticle);
   }

   public function getPageType(): string {
      return $this->wcPacket->getPageType();
   }

   public function getPageid(): int {
      return $this->wcPacket->getPageid();
   }

   public function getWidgetCollectionPacket(): WidgetCollectionPacket {
      return $this->wcPacket;
   }
}
