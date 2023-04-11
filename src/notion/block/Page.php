<?php

namespace b3co\notion\block;

use b3co\notion\block\RichText;
use b3co\notion\block\Block;
use b3co\notion\Notion;

use b3co\notion\block\interfaces\BlockInterface;

class Page extends Block {
  public $id;
  public $title = '';
  public $body  = '';
  public $icon  = '';
  public $type  = '';
  public $cover = null;

  public $children = [];

  public $toc;
  public $has_toc = false;

  private $blocks;
  private $title_object;

  public function __construct($id, $notion, $upload = false) {
    $this->notion = $notion;
    $url  = $this->notion->retrieve('get_page', ['page_id' => $id]);
    $data = json_decode($url, true);

    $this->id     = $data['id'];
    $this->type   = 'page';
    $this->upload = $upload;

    $this->icon   = $data['icon']['emoji'];

    if($data['cover']) {
      $this->cover = new Image($data, $this);
    }

    $this->title_object = new RichText($data['properties']['title']['title']);
    $this->title = $this->title_object->getPlainText();

    $this->children = $notion->getChildren($this->id, $this);
  }

  public function getChildrenPages() {
    return $this->getChildrenByType('child_page');
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
    $this->children_body = $this->getChildrenBody();
    if($this->hasTemplate('html')) {
      return $this->renderTemplate('html');
    }

    $ret  = sprintf("<title>%s %s</title>\n", $this->icon, $this->title);

    if($this->cover) {
      $ret .= sprintf("<img src='%s' style='width: 100%%; height: 200px; overflow: hidden; text-align: center;'>\n",
        $this->cover->url);
    }

    if($this->has_toc) {
      $ret .= $this->toc->toHtml();
    }

    foreach($this->children as $b) {
      $ret .= sprintf("%s\n", $b->toHtml());
    }

    return $ret;
  }

  public function addHeading($block) {
    $this->toc->elements[] = $block;
  }

  public function getRaw() {
    return $this->raw;
  }

  public function toTemplate($template, $file = null) {
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
