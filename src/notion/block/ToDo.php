<?php

namespace b3co\notion\block;

class ToDo extends Block implements BlockInterface {

  public $checked = false;

  private $text_object;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->text_object = new RichText($data['to_do']['rich_text']);
    $this->checked = $data['to_do']['checked'] == 1;
  }

  public function toString() {
    return sprintf("[%s] %s", $this->checked?"x":" ", $this->text_object->getPlainText());
  }

  public function toMarkDown() {
    return sprintf("- [%s] %s", $this->checked?"x":" ", $this->text_object->getMarkDown());
  }

  public function toHtml($container = 'div') {
    $text = $this->text_object->getHtml('');
    if($this->checked) {
      $text = sprintf("<strike>%s</strike>", $text);
    }
    $ret = sprintf("<input type=checkbox%s> %s",
      $this->checked?" checked":"", $text);
    return sprintf(Block::$html_containers[$container], $ret);
  }
}
