<?php

namespace b3co\notion\block;

require_once("BlockInterface.php");

class Paragraph extends Block implements BlockInterface {

  public  $plain_text;
  private $text_object;

  public function __construct($data, $upload = false) {
    parent::__construct($data);
    $this->text_object = new RichText($data['paragraph']['rich_text']);
  }

  public function toString() {
    return $this->text_object->getPlainText();
  }

  public function toMarkDown() {
    return $this->text_object->getMarkDown();
  }
}