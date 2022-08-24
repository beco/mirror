<?php

namespace b3co\notion\block;

class ChildPage extends Block implements BlockInterface {

  public $title;
  public $page;
  public $url;
  public $page_id;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent, false);
    $this->title   = $data['child_page']['title'];
    $this->page_id = $data['id'];
  }

  public function toString() {
    return sprintf("%s (%s)", $this->title, $this->id);
  }

  public function toMarkDown() {
    return sprintf("%s (%s)", $this->title, $this->id);
  }

  public function toHtml($container = 'div') {
    return sprintf("<p>%s (%s)</p>", $this->title, $this->id);
  }
}
