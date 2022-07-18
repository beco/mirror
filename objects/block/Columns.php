<?php

namespace b3co\notion\block;

require_once("BlockInterface.php");

class Columns extends Block implements BlockInterface {

  public $columns;

  public function __construct($data, $upload = false) {
    parent::__construct($data, $upload);
    printf("has %d columns\n", count($this->children));
  }

  public function toString() {
    $ret = 'Column set:\n';
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
    $ret = "<table style='width: 100%; border:0px'><tr>\n";
    foreach($this->children as $block) {
      $ret .= sprintf("<td width='%d%%' style='border: 0px'>%s</td>\n", 100/count($this->children), $block->toHtml('none'));
    }
    $ret .= "</tr></table>";
    return $ret;
  }
}
