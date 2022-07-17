<?php

namespace b3co\notion\block;

class Block {
  public $id;
  public $type;

  private $raw;
  private $notion;
  private $elements;

  public function __construct($data) {
    $this->id   = $data['id'];
    $this->type = $data['type'];
    $this->raw  = $data;
  }

  public function getRaw() {
    return $this->raw;
  }
}
