<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class TableOfContents extends Block implements BlockInterface {

  private $headings = [];

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->parent_page->has_toc = true;

    //not quite fan of such a circular reference, but...
    $this->parent_page->toc = $this;
  }

  public function addHeading($heading) {
    $this->headings[] = $heading;
  }

  public function toHtml() {
    $ret  = "<p>table of contents</p>\n";
    //
    $last_level = 0;
    $open = 0;
    foreach($this->headings as $h) {
      if($h->level > $last_level) {
        $ret .= "<ul>\n";
        $open++;
      }
      if($h->level < $last_level) {
        $ret .= "</ul>\n";
        $open--;
      }
      $last_level = $h->level;
      $ret .= $h->toTocElement();
    }

    for($i = 0; $i < $open; $i++) {
      $ret .= "</ul>\n";
    }
    //
    return $ret;
  }

  public function toMarkDown() {}

  public function toString() {}
}
