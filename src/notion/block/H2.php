<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class H2 extends Heading implements BlockInterface {

  public $text_object;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->level = '2';
    $this->text_object = new RichText($data['heading_2']['rich_text']);
  }
}
