<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class Paragraph extends Block implements BlockInterface {

  public  $plain_text;
  public  $text;
  private $text_object;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->text_object = new RichText($data['paragraph']['rich_text']);
  }

  public function toString() {
    return $this->text_object->getPlainText();
  }

  public function toMarkDown() {
    return $this->text_object->getMarkDown();
  }

  public function toHtml($container = 'div') {
    $this->text = $this->text_object->getHtml();
    if($this->hasTemplate('html')) {
      return $this->renderTemplate('html');
    }
    return sprintf("%s", $this->text);
  }
}
