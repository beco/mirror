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
    return sprintf("* %s\n", $this->text_object->getPlainText());
  }

  public function toMarkDown() {
    return sprintf("* %s", $this->text_object->getMarkDown());
  }

  public function toHtml($container = 'div') {
    return sprintf("%s  <li>%s</li>%s",
      $this->is_first?"<ul>\n":"",
      $this->text_object->getHtml(),
      $this->is_last?"\n</ul>":"",
    );
  }
}
