<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

use HtmlFramework\Packet\PacketTrait;

class ArticlePacket {
   private $pageid;
   use PacketTrait;

   /**
    * @param string $articlePath - Path to the phtml file we want to print
    * @param array $pageData - Should only have the data we want to display in the article
    * @param array $mainArticle - The main text that will be parsed into html and displayed on the page
    */
   public function __construct(int $pageid, string $articlePath, array $articleData, string $mainArticle) {
      $this->pageid = $pageid;
      $this->setData('articlePath', $articlePath);
      $this->setData('articleData', $articleData);
      $this->setData('mainArticle', $mainArticle);
   }

   public function getPageid(): int {
      return $this->pageid;
   }
}
