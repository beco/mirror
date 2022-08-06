<?php

namespace b3co\notion\block;

class TableCell extends Block implements BlockInterface {

  public $content;

  public function __construct($data, $parent, $upload = false) {
    parent::__construct($data, $parent);
    $this->content = new RichText($data);
  }

  public function toString() {
    return sprintf("%s | ", $this->content->getMarkDown());
  }

  public function toMarkDown() {
    return sprintf("%s | ", $this->content->getMarkDown());
  }

  public function toHtml() {
    return sprintf("    <td>\n     %s\n    </td>\n", $this->content->getHtml());
  }
}
