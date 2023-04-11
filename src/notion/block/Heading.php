<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class Heading extends Block implements BlockInterface {

  public $text_object;
  public $level;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
  }

  public function toString() {
    return sprintf("%s\n", $this->text_object->getPlainText());
  }

  public function toMarkDown() {
    $prefix = '';
    for($i = 0; $i < $this->level; $i++) {
      $prefix .= '#';
    }
    return sprintf("%s %s", $prefix, $this->text_object->getMarkDown());
  }

  public function toHtml($container = 'div') {
    $this->children_body = $this->getChildrenBody();
    if($this->hasTemplate('html')) {
      return $this->renderTemplate('html');
    }
    $ret = sprintf("<a name='a_%s'>\n<h%s>%s</h%s>\n",
      $this->id,
      $this->level,
      $this->text_object->getHtml(),
      $this->level
    );
    if($this->has_children) {
      $ret .= sprintf("%s\n", $this->children_body);
    }
    return $ret;
  }
}
