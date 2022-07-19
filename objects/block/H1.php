<?php

namespace b3co\notion\block;

require_once("Interfaces.php");

class H1 extends Block implements BlockInterface {

  public $text_object;

  public function __construct($data, $parent, $upload = false) {
    parent::__construct($data, $parent);
    $this->text_object = new RichText($data['heading_1']['rich_text']);
  }

  public function toString() {
    return sprintf("%s\n", $this->text_object->getPlainText());
  }

  public function toMarkDown() {
    return sprintf("# %s", $this->text_object->getMarkDown());
  }

  public function toHtml($container = 'div') {
    return sprintf("<h1>%s</h1>", $this->text_object->getHtml());
  }
}
