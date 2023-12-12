<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class Columns extends Block implements BlockInterface {

  public $columns;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
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
    $this->children_body = $this->getChildrenBody();
    if($this->hasTemplate('html')) {
      return $this->renderTemplate('html');
    }
    $ret = "<table style='width: 100%; border:0px'><tr>\n";
    $ret .= $this->getChildrenBody();
    $ret .= "</tr></table>";
    return $ret;
  }

  public function getChildrenBodys() {
    $ret = "";
    foreach($this->children as $block) {
      $ret .= sprintf("<td width='%d%%' style='border: 0px; vertical-align: top; padding: 0px; padding-right: 5px; margin: 0px;'>%s</td>\n", 100/count($this->children), $block->toHtml('none'));
    }
    return $ret;
  }
}
