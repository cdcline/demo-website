<?php

namespace HtmlFramework;

use Utils\ServerUtils;
use DOMDocument;
use DOMElement;

abstract class HeaderElement {
   protected const STATIC_IMAGE_PATH = 'src/images/site/';

   public static function getHtml(array $params = []): string
   {
      return (new static($params))->buildHtml();
   }

   abstract protected function buildHtml(): string;

   protected function getImage(string $imageName, bool $forceStatic = true): string {
      $useStatic = $forceStatic || !ServerUtils::onGoogleCloudProject();
      return $useStatic ? static::getStaticImage($imageName) : static::getHostedImage($imageName);

   }

   protected static function getStaticImage(string $imagePath): string {
      return self::STATIC_IMAGE_PATH . $imagePath;
   }

   protected static function getHostedImage(string $imagePath): string {
      return ServerUtils::getHostedImagePath() . "{$imagePath}";
   }

   protected function addAttribute(DOMDocument $dom, DomElement $element, string $attributeName, string $attributeValue): void {
      $dAttribute = $dom->createAttribute($attributeName);
      $dAttribute->value = $attributeValue;
      $element->appendChild($dAttribute);
   }
}