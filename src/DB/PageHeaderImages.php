<?php declare(strict_types=1);

namespace DB;

use DB\DBTrait;
//use Exception;
use HtmlFramework\Packet\HeaderPacket;
//use Utils\FirestoreUtils;
use Utils\HtmlUtils;
use Utils\ServerUtils;

class PageHeaderImages {
   use DBTrait;

   private static $pageHeaderStaticCache = [];

   private const RANDOM_MARGIN = 10;
   private $pageHeaderImages;

   public static function fromPageid(int $pageid): self {
      $cache = self::getStaticCache() ?: [];
      if (isset($cache[$pageid])) {
         return $cache[$pageid];
      }
      $imageValues = /*ServerUtils::useBackendDB() ? self::getPageHeaderImages($pageid) : */self::getRowsForPageid($pageid);
      $pHeaderImages = self::fromValues($imageValues);
      $cache[$pageid] = $pHeaderImages;
      self::setStaticCache($cache);
      return $pHeaderImages;
   }

   private static function fromValues(array $images): self {
      array_multisort(array_column($images, 'orderby'), SORT_ASC, $images);
      $generateImage = function(array $image) {
         return PageHeaderImage::fromValues($image ?? []);
      };
      return new self(array_map($generateImage, $images));
   }

   public function isCarousel(): bool {
      return count($this->pageHeaderImages) > 1;
   }

   public function toFullArray(): array {
      $toFullArray = function(PageHeaderImage $image) {
         return $image->toFullArray();
      };
      return array_map($toFullArray, $this->pageHeaderImages);
   }

   public function toMobileArray(): array {
      $toMobileArray = function(PageHeaderImage $image) {
         return $image->toMobileArray();
      };
      return array_map($toMobileArray, $this->pageHeaderImages);
   }

   private function __construct(array $pageHeaderImages) {
      $this->pageHeaderImages = $pageHeaderImages;
   }

   /*
   private static function getPageHeaderImages(int $pageid): array {
      // We're gonna pull these values from each `page_header_images` collection
      $imageDocValues = ['full_src', 'mobile_src', 'orderby'];
      // Go through all our index pages find the "firestoreid" by the "pageid"
      // so we can grab the page's image data
      $iPagesPath = FirestoreUtils::indexPagesPath();
      $iDocs = ['pageid'];
      foreach (self::fetchRows($iPagesPath, $iDocs) as $iPage) {
         if ((int)$iPage['pageid'] !== $pageid) {
            continue;
         }
         $fId = $iPage['firestoreId'];
         $imagesPath = FirestoreUtils::headerImagesPath($fId);
         try {
            $imageData = self::fetchRows($imagesPath, $imageDocValues);
         } catch (Exception $e) {}
      }
      return $imageData ?: [];
   }
   */

   private static function getDevStaticData(): array {
      return self::getHardcodedRows();
   }

   private static function getLiveStaticData(): array {
      $getLifeHeaderImage = function($orderby) {
         return self::getHostedImage("life_header/life_{$orderby}.jpeg");
      };
      $getMobileLifeHeaderImage = function($orderby) {
         return self::getHostedImage("life_header/life_mobile_{$orderby}.jpeg");
      };
      return [
         1 => [
            ['full_src' => self::getHostedImage('welcome.png'),
             'mobile_src' => self::getHostedImage('welcome_mobile.png'),
             'orderby' => 1,
            ],
         ],
         2 => [
            ['full_src' => self::getHostedImage('work.png'),
             'mobile_src' => self::getHostedImage('work_mobile.png'),
             'orderby' => 1,
            ],
         ],
         3 => [
            ['full_src' => self::getHostedImage('robots.png'),
             'mobile_src' => self::getHostedImage('robots_mobile.png'),
             'orderby' => 1,
            ],
         ],
         4 => [
            ['full_src' => self::getHostedImage('life.png'),
             'mobile_src' => self::getHostedImage('life_mobile.png'),
             'orderby' => 1,
            ],
         ],
         4 => [
            ['full_src' => $getLifeHeaderImage(1),
             'mobile_src' => $getMobileLifeHeaderImage(1),
             'orderby' => 1,
            ],
            ['full_src' => $getLifeHeaderImage(2),
             'mobile_src' => $getMobileLifeHeaderImage(2),
             'orderby' => 2,
            ],
            ['full_src' => $getLifeHeaderImage(3),
             'mobile_src' => $getMobileLifeHeaderImage(3),
             'orderby' => 3,
            ],
            ['full_src' => $getLifeHeaderImage(4),
             'mobile_src' => $getMobileLifeHeaderImage(4),
             'orderby' => 4,
            ]
         ]
      ];
   }

   private static function getRowsForPageid(int $pageid): array {
      $staticRows = self::getStaticData();
      return $staticRows[$pageid] ?? [];
   }

   private static function getHardcodedRows(): array {
      return [
         1 => [
            ['full_src' => self::getRandomFullHeaderImgSrc(),
             'mobile_src' => self::getRandomMobileHeaderImgSrc(),
             'orderby' => 1,
            ],
         ],
         4 => [
            ['full_src' => self::getRandomFullHeaderImgSrc(),
             'mobile_src' => self::getRandomMobileHeaderImgSrc(),
             'orderby' => 1,
            ],
            ['full_src' => self::getRandomFullHeaderImgSrc(),
             'mobile_src' => self::getRandomMobileHeaderImgSrc(),
             'orderby' => 2,
            ],
            ['full_src' => self::getRandomFullHeaderImgSrc(),
             'mobile_src' => self::getRandomMobileHeaderImgSrc(),
             'orderby' => 3,
            ],
            ['full_src' => self::getRandomFullHeaderImgSrc(),
             'mobile_src' => self::getRandomMobileHeaderImgSrc(),
             'orderby' => 4,
            ]
         ]
      ];
   }

   private static function getHostedImage($imageName): string {
      return ServerUtils::getHostedImagePath() . $imageName;
   }

   private static function getRandomFullHeaderImgSrc(): string {
      $width = rand(HeaderPacket::FULL_WIDTH - self::RANDOM_MARGIN, HeaderPacket::FULL_WIDTH + self::RANDOM_MARGIN);
      $height = rand(HeaderPacket::FULL_HEIGHT - self::RANDOM_MARGIN, HeaderPacket::FULL_HEIGHT + self::RANDOM_MARGIN);
      return HtmlUtils::getPicsumPhoto($width, $height);
   }

   private static function getRandomMobileHeaderImgSrc(): string {
      $width = rand(HeaderPacket::MOBILE_WIDTH - self::RANDOM_MARGIN, HeaderPacket::MOBILE_WIDTH + self::RANDOM_MARGIN);
      $height = rand(HeaderPacket::MOBILE_HEIGHT - self::RANDOM_MARGIN, HeaderPacket::MOBILE_HEIGHT + self::RANDOM_MARGIN);
      return HtmlUtils::getPicsumPhoto($width, $height);
   }
}

class PageHeaderImage {
   private $fullSrc;
   private $mobileSrc;
   private $position;

   public static function fromValues(array $iData): self {
      $position = isset($iData['orderby']) ? (int)$iData['orderby'] : 0;
      return new self($iData['full_src'] ?? '', $iData['mobile_src'] ?? '', $position);
   }

   public function toFullArray(): array {
      return array_merge([
         'src' => $this->fullSrc,
      ], $this->getCommonValues());
   }

   public function toMobileArray(): array {
      return array_merge([
         'src' => $this->mobileSrc,
      ], $this->getCommonValues());
   }

   private function getCommonValues(): array {
      return [
         'position' => $this->position
      ];
   }

   private function __construct(string $fullSrc, string $mobileSrc, int $position) {
      $this->fullSrc = $fullSrc;
      $this->mobileSrc = $mobileSrc;
      $this->position = $position;
   }
}
