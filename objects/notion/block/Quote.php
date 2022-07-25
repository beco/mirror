<?php

namespace b3co\notion\block;

class Quote extends Block implements BlockInterface {

  public $text_object;

  public function __construct($data, $parent, $upload = false) {
    parent::__construct($data, $parent, $upload);
    $this->text_object = new RichText($data['quote']['rich_text']);
  }

  public function toString() {
    return sprintf("> %s\n", $this->text_object->getPlainText());
  }

  public function toMarkDown() {
    return sprintf("> %s\n", $this->text_object->getMarkDown());
  }

  public function toHtml($container = 'div') {
    $ret = sprintf("<blockquote>%s</blockquote>", $this->text_object->getHtml('none'));
    return $ret;
  }
}
