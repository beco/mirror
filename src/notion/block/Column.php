<?php

namespace b3co\notion\block;

class Column extends Block implements BlockInterface {

  public $text_object;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
  }

  public function toString() {
    $ret = '';
    if($this->has_children) {
      foreach($this->children as $block) {
        $ret .= sprintf("%s\n", $block->toString());
      }
    }
    return $ret;
  }

  public function toMarkDown() {
    $ret = '';
    if($this->has_children) {
      foreach($this->children as $block) {
        $ret .= sprintf("%s\n", $block->toMarkDown());
      }
    }
    return $ret;
  }

  public function toHtml($container = 'div') {
    $ret = '';
    if($this->has_children) {
      foreach($this->children as $block) {
        $ret .= sprintf("%s\n", $block->toHtml());
      }
    }
    return $ret;
  }
}
