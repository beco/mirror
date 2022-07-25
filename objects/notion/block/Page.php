<?php

namespace b3co\notion\block;

use b3co\notion\block\RichText;
use b3co\notion\block\Block;
use b3co\notion\Notion;

class Page extends Block {
  public $id;
  public $title = '';
  public $body  = '';
  public $icon  = '';
  public $type  = '';
  public $cover = null;

  public $children = [];
  public $notion;

  private $raw  = '';
  private $blocks;
  private $title_object;

  public function __construct($id, $upload = false, $notion) {
    $this->notion = $notion;
    $data = json_decode($this->notion->retrieve('get_page', ['page_id' => $id]), true);

    $this->raw   = $data;
    $this->id    = $data['id'];
    $this->type  = 'page';

    $this->icon  = $data['icon']['emoji'];

    if($data['cover']) {
      $this->cover = new Image($data, $this, $upload);
    }

    $this->title_object = new RichText($data['properties']['title']['title']);
    $this->title = $this->title_object->getPlainText();

    $this->children = $notion->getChildren($this->id, $this, $upload);
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
    $ret  = sprintf("<title>%s %s</title>\n", $this->icon, $this->title);

    if($this->cover) {
      $ret .= sprintf("<img src='%s' style='width: 100%%; height: 200px; overflow: hidden; text-align: center;'>\n",
        $this->cover->url);
    }

    foreach($this->children as $b) {
      $ret .= sprintf("%s\n", $b->toHtml());
    }

    return $ret;
  }

  public function getRaw() {
    return $this->raw;
  }

  public function toTemplate($template) {
    $this->body = $this->getTemplateBody($template);
    //echo $this->body;
    return $this->renderTemplate($template);
  }

  private function getTemplateBody($template) {
    $ret = '';
    foreach($this->children as $b) {
      $ret .= $b->toTemplate($template);
    }
    return $ret;
  }
}