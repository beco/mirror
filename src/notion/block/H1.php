<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class H1 extends Heading implements BlockInterface {

  public $text_object;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->level = 1;
    $this->text_object = new RichText($data['heading_1']['rich_text']);
  }
}
