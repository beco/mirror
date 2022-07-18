<?php

namespace b3co\notion\block;

require_once("BlockInterface.php");

class Divider extends Block implements BlockInterface {

  public function __construct($data, $upload = false) {
    parent::__construct($data);
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
