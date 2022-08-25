<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class Code extends Block implements BlockInterface {

  public $text_object;
  public $lines = 0;
  public $language;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->text_object = new RichText($data['code']['rich_text']);
    $this->lines = count(explode("\n", $this->text_object->getPlainText()));
    $this->language = isset($data['code']['language'])?$data['code']['language']:'';
  }

  public function toString() {
    return sprintf("%s\n", $this->text_object->getPlainText());
  }

  public function toMarkDown() {
    if($this->lines == 1)
      return sprintf("`%s`", $this->text_object->getPlainText());
    return sprintf("```%s\n%s\n```",
      $this->language,
      $this->text_object->getPlainText());
  }

  public function toHtml($container = 'div') {
    if($this->hasTemplate('html')) {
      return $this->renderTemplate('html');
    }
    return sprintf("<code>%s</code>", $this->text_object->getHtml());
  }
}
