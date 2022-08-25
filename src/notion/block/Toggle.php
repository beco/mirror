<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class Toggle extends Block implements BlockInterface {

  public $text_object;
  public $title;
  public $children_body;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->text_object = new RichText($data['toggle']['rich_text']);
    $this->title = $this->text_object->getHtml();
  }

  public function toString() {
    return sprintf("%s\n", $this->text_object->getPlainText());
  }

  public function toMarkDown() {
    return sprintf("## %s", $this->text_object->getMarkDown());
  }

  public function toHtml($container = 'div') {
    $this->children_body = $this->getChildrenBody();
    if($this->hasTemplate('html')) {
      return $this->renderTemplate('html');
    }
    return sprintf("%s", $this->text_object->getHtml());
  }
}
