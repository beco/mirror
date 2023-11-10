<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;
use b3co\notion\utils\Color;

class Callout extends Block implements BlockInterface {

  public $icon;
  public $color;
  public $content;
  private $text_object;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->icon  = $data['callout']['icon']['emoji'];
    $this->color = $data['callout']['color'];
    $this->text_object = new RichText($data['callout']['rich_text']);
  }

  public function toString() {
    return sprintf("[%s] %s", $this->icon, $this->text_object->toString());
  }

  public function toMarkDown() {
    return sprintf("> %s %s\n", $this->icon, $this->text_object->getMarkDown());
  }

  public function toHtml($container = 'div') {
    return sprintf("<div class='callout' style='background-color:%s;border-radius:20px;padding:20px;margin-bottom:10px'>%s&nbsp;&nbsp;%s</div>\n",
      Color::getHex($this->color),
      $this->icon,
      $this->text_object->getHtml()
    );
  }
}
