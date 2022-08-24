<?php

namespace b3co\notion\block;

class Divider extends Block implements BlockInterface {

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
  }

  public function toString() {
    return "----------------------------\n";
  }

  public function toMarkDown() {
    return "---\n";
  }

  public function toHtml($container = 'div') {
    return "<hr width='90%'>\n";
  }
}
