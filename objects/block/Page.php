<?php

namespace b3co\notion\block;

use b3co\notion\block\RichText;
use b3co\notion\Notion;

class Page {
  public $id;
  public $title = '';
  public $icon  = '';
  public $children = [];

  private $raw  = '';
  private $notion;
  private $blocks;
  private $title_object;

  public function __construct($id, $upload = false) {
    // refactor to use/access Notion object in a smarter way
    $notion = new Notion;

    $data = json_decode($notion->getPage($id), true);

    $this->raw   = $data;
    $this->id    = $data['id'];
    $this->icon  = $data['icon']['emoji'];

    $this->title_object = new RichText($data['properties']['title']['title']);
    $this->title = $this->title_object->getPlainText();

    $this->children = $notion->getChildren($this->id, $upload);
  }

  public function toString() {
    $ret = sprintf("%s %s\n", $this->icon, $this->title);
    foreach($this->children as $b) {
      $ret .= sprintf("- %s\n", $b->toString());
    }
    return $ret;
  }

  public function toMarkDown() {
    $ret  = sprintf("%s %s\n", $this->icon, $this->title);
    $ret .= str_repeat("=", strlen($ret));
    $ret .= "\n";
    foreach($this->children as $b) {
      $ret .= sprintf("%s\n", $b->toMarkDown());
    }
    return $ret;
  }

  public function toHtml() {
    $ret = sprintf("<title>%s %s</title>\n", $this->icon, $this->title);
    foreach($this->children as $b) {
      $ret .= sprintf("%s\n", $b->toHtml());
    }

    return $ret;
  }

  public function getRaw() {
    return $this->raw;
  }
}
