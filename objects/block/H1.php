<?php

namespace b3co\notion\block;

require_once("BlockInterface.php");

class H1 extends Block implements BlockInterface {

  public $text;

  public function __construct($data) {
    parent::__construct($data);
    $this->text = $data['heading_1']['rich_text'][0]['plain_text'];
  }

  public function toString() {
    return sprintf("%s\n", $this->text);
  }

  public function toMarkDown() {
    return sprintf("# %s", $this->text);
  }
}
