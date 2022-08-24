<?php

namespace b3co\notion\block;

class TableRow extends Block implements BlockInterface {

  public $cells = [];
  public $is_header = false;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);

    foreach($data['table_row']['cells'] as $c) {
      $this->cells[] = new TableCell($c, $parent);
    }
  }

  public function toString() {
    $ret = "";
    foreach($this->cells as $c) {
      $ret .= $c->toMarkDown();
    }
    return sprintf("| %s \n", $ret);
  }

  public function toMarkDown() {
    $ret = "";
    foreach($this->cells as $c) {
      $ret .= $c->toMarkDown();
    }
    return sprintf("| %s \n", $ret);
  }

  public function toHtml() {
    $ret = "";
    foreach($this->cells as $c) {
      $ret .= $c->toHtml();
    }

    if($this->is_header) {
      $ret = sprintf("  <th>\n%s\n  </th>\n", $ret);;
    } else {
      $ret = sprintf("  <tr>\n%s\n  </tr>\n", $ret);
    }
    return $ret;
  }
}
