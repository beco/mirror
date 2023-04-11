<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class H3 extends Heading implements BlockInterface {

  public $text_object;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->level = 3;
    $this->text_object = new RichText($data['heading_3']['rich_text']);
  }
}
