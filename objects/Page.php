<?php

namespace b3co\notion;

class Page {
  public $id;
  public $title = '';
  public $icon  = '';

  private $raw  = '';
  private $notion;
  private $blocks;

  public function __construct($id, $upload = false) {
    $notion = new Notion;

    $data = json_decode($notion->getPage($id), true);

    $this->raw   = $data;
    $this->id    = $data['id'];
    $this->title = $data['properties']['title']['title'][0]['plain_text'];
    $this->icon  = $data['icon']['emoji'];

    $blocks = json_decode($notion->getNodesFrom($this->id), true);
    foreach($blocks['results'] as $block) {
      if(!Notion::$classes[$block['type']]) {
        if(VERBOSE) printf("no class for %s\n", $block['type']);
      } else {
        if(VERBOSE) printf("instantiating %s\n", $block['type']);
        $this->blocks[] = new Notion::$classes[$block['type']]($block, $upload);
      }
    }
  }

  public function toString() {
    $ret = sprintf("%s %s\n", $this->icon, $this->title);
    foreach($this->blocks as $b) {
      $ret .= sprintf("- %s\n", $b->toString());
    }
    return $ret;
  }

  public function toMarkDown() {
    $ret  = sprintf("%s %s\n", $this->icon, $this->title);
    $ret .= str_repeat("=", strlen($ret));
    $ret .= "\n";
    foreach($this->blocks as $b) {
      $ret .= sprintf("%s\n", $b->toMarkDown());
    }
    return $ret;
  }

  public function getRaw() {
    return $this->raw;
  }
}
