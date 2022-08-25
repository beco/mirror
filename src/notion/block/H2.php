<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class H2 extends Block implements BlockInterface {

  public $text_object;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->text_object = new RichText($data['heading_2']['rich_text']);
  }

  public function toString() {
    return sprintf("%s\n", $this->text_object->getPlainText());
  }

  public function toMarkDown() {
    return sprintf("## %s", $this->text_object->getMarkDown());
  }

  public function toHtml($container = 'div') {
    $this->children_body = $this->getChildrenBody();
    if($this->hasTemplate('html')) {
      return $this->renderTemplate('html');
    }
    $ret = sprintf("<h2>%s</h2>\n", $this->text_object->getHtml());
    if($this->has_children) {
      $ret .= sprintf("%s\n", $this->children_body);
    }
    return $ret;
  }
}
