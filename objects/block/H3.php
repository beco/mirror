<?php

namespace b3co\notion\block;

require_once("BlockInterface.php");

class H3 extends Block implements BlockInterface {

  public $text_object;

  public function __construct($data, $upload = false) {
    parent::__construct($data);
    $this->text_object = new RichText($data['heading_3']['rich_text']);
  }

  public function toString() {
    return sprintf("%s\n", $this->text_object->getPlainText());
  }

  public function toMarkDown() {
    return sprintf("### %s", $this->text_object->getMarkDown());
  }

  public function toHtml($container = 'div') {
    return sprintf("<h3>%s</h3>", $this->text_object->getHtml());
  }
}
