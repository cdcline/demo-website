<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Packet\PacketTrait;

class ArticlePacket {
   use PacketTrait;

   /**
    * @param string $articlePath - Path to the phtml file we want to print
    * @param array $pageData - Should only have the data we want to display in the article
    * @param array $mainArticle - The main text that will be parsed into html and displayed on the page
    */
   public function __construct(string $articlePath, array $articleData, string $mainArticle) {
      $this->setData('articlePath', $articlePath);
      $this->setData('articleData', $articleData);
      $this->setData('mainArticle', $mainArticle);
   }
}
