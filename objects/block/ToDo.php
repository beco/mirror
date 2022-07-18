<?php

namespace b3co\notion\block;

require_once("BlockInterface.php");

class ToDo extends Block implements BlockInterface {

  public $text = '';
  public $checked = false;

  public function __construct($data, $upload = false) {
    parent::__construct($data);
    $this->text = $data['to_do']['rich_text'][0]['plain_text'];
    $this->checked = $data['to_do']['checked'] == 1;
  }

  public function toString() {
    return sprintf("[%s] %s", $this->checked?"x":" ", $this->text);
  }

  public function toMarkDown() {
    return sprintf("[%s] %s", $this->checked?"x":" ", $this->text);
  }
}
