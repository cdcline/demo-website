<?php declare(strict_types=1);

namespace HtmlFramework\Packet;

trait PacketTrait {
   private $data = [];

   public function getData(string $index) {
      return isset($this->data[$index]) ? $this->data[$index] : null;
   }

   public function setData(string $index, $value): void {
      $this->data[$index] = $value;
   }
}
