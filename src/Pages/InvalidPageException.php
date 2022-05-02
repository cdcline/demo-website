<?php declare(strict_types=1);

namespace Pages;

use Exception;
use Utils\StringUtils;

class InvalidPageException extends Exception {
   // Doesn't really matter, just making unique & not 0
   const EXCEPTION_CODE = 1;
   private $eString;
   private $pageFindError;

   /**
    * @param string|int $input - We can try to find a page by slug or pageid.
    */
   public static function throwPageNotFound($input): void {
      throw new InvalidPageException((string)$input, /*pageFindError*/true);
   }

   public static function throwInvalidPageOperation(string $eString): void {
      throw new InvalidPageException((string)$eString);
   }

   private function __construct(string $eString, bool $pageFindError = false) {
      $this->eString = $eString ?: 'Warning: Blank input.';
      $this->pageFindError = $pageFindError;
      parent::__construct($this->getCustomMessage(), self::EXCEPTION_CODE);
   }

   private function getCustomMessage(): string {
      return $this->pageFindError ? "Unkown page: {$this->eString}" : $this->eString;
   }
}
