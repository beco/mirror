<?php

namespace b3co\notion\block;

require_once("BlockInterface.php");

class ToDo extends Block implements BlockInterface {

  public $checked = false;

  private $text_object;

  public function __construct($data, $upload = false) {
    parent::__construct($data, $upload);
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
    $ret = sprintf("<input type=checkbox%s>%s",
      $this->checked?" checked":"", $this->text_object->getHtml(''));
    return sprintf(Block::$html_containers[$container], $ret);
  }
}
