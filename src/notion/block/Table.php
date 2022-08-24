<?php

namespace b3co\notion\block;

class Table extends Block implements BlockInterface {

  public $width;
  public $has_column_header;
  public $has_row_header;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->width = $data[$this->type]['table_width'];

    $this->has_column_header = $data[$this->type]['has_column_header'];
    $this->has_row_header    = $data[$this->type]['has_row_header'];
  }

  public function toString() {
    $ret = "";
    foreach($this->children as $row) {
      $ret .= $row->toMarkDown();
    }
    return sprintf("%s", $ret);
  }

  public function toMarkDown() {
    $ret = "";
    foreach($this->children as $row) {
      $ret .= $row->toMarkDown();
    }
    return sprintf("%s", $ret);
  }

  public function toHtml() {
    $ret = "";
    foreach($this->children as $row) {
      $ret .= $row->toHtml();
    }
    return sprintf("<table>\n%s\n</table>", $ret);
  }
}
