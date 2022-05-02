<?php declare(strict_types=1);

namespace Pages;

use Exception;

class InvalidPageException extends Exception {
   // Doesn't really matter, just making unique & not 0
   const EXCEPTION_CODE = 1;
   private $pageid;

   /**
    * NOTE: This should be just the $pageid but we'll be supporting the $slug for a bit
    * while we don't really support many pages.
    */
   public function __construct(string $pageid) {
      $this->pageid = $pageid;
      parent::__construct($this->getCustomMessage(), self::EXCEPTION_CODE);
   }

   private function getCustomMessage(): string {
      return "Invalid pageid: {$this->pageid}";
   }
}
