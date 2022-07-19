<?php

namespace b3co\notion\block;

require_once("Interfaces.php");

class Paragraph extends Block implements BlockInterface {

  public  $plain_text;
  private $text_object;

  public function __construct($data, $parent, $upload = false) {
    parent::__construct($data, $parent, $upload);
    $this->text_object = new RichText($data['paragraph']['rich_text']);
  }

  public function toString() {
    return $this->text_object->getPlainText();
  }

  public function toMarkDown() {
    return $this->text_object->getMarkDown();
  }

  public function toHtml($container = 'div') {
    return sprintf(Block::$html_containers[$container], $this->text_object->getHtml());
  }
}
