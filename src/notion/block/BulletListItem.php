<?php

namespace b3co\notion\block;
use b3co\notion\block\interfaces\BlockInterface;

class BulletListItem extends Block implements BlockInterface {

  public $text_object;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->text_object = new RichText($data['bulleted_list_item']['rich_text']);
  }

  public function toString() {
    $ret = sprintf("* %s", $this->text_object->getPlainText());
    foreach($this->children as $child) {
      $ret .= sprintf("\n  %s", $child->toString());
    }
    return $ret;
  }

  public function toMarkDown() {
    $ret = sprintf("* %s", $this->text_object->getMarkDown());
    foreach($this->children as $child) {
      $ret .= sprintf("\n  %s", $child->toMarkDown());
    }
    return $ret;
  }

  public function toHtml($container = 'div') {
    return sprintf("%s  <li>%s%s</li>%s\n",
      $this->is_first?"<ul>\n":"",
      $this->text_object->getHtml(),
      $this->getChildrenHtml(),
      $this->is_last?"\n</ul>":"",
    );
  }

  private function getChildrenHtml() {
    $ret = '';
    foreach($this->children as $child) {
      $ret .= $child->toHtml();
    }
    return $ret;
  }
}
