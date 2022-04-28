<?php declare(strict_types=1);

namespace HtmlFramework;

/**
 * This "HTML Element" will allow us collect all the framework parts
 * of the html layout under a single folder b/c there's several of them.
 *
 * It also provides a handly print function all the elemnts will use to
 * output the text to display to the user.
 **/
abstract class Element {
   protected $packet;
   /**
    * These "framework elements" should be pretty similar but there will be a
    * lot of them so we'll stash them in this folder.
    */
   private const HTML_FRAMEWORK_PATH = 'src/templates/framework';

   /**
    * Kinda a silly way to just get a small constant string from each class
    * but imo it helps to always use a function and not rely on static info
    * which can get confusing for parsers and people.
    */
   abstract protected function getFrameworkFile(): string;

   /**
    * This is where we actually print text to the user.
    */
   public function printHtml(): void {
      require $this->getFrameworkFilePath();
   }

   /**
    * We don't want to define the whole path in each class so we'll use
    * this as a shim.
    *
    * Abstracted out b/c I think it makes things easier to understand.
    */
   private function getFrameworkFilePath(): string {
      return self::HTML_FRAMEWORK_PATH . "/{$this->getFrameworkFile()}";
   }

   protected function getPacketData(string $index) {
      return $this->packet->getData($index);
   }
}
