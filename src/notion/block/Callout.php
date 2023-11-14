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
    $ret = '';
    if($this->has_children) {
      foreach($this->children as $block) {
        $ret .= sprintf("%s\n", $block->toString());
      }
    }
    return $this->icon . " " . $ret;
  }

  public function toMarkDown() {
    if($this->has_children != true) {
        return sprintf("> %s %s\n", $this->icon, $this->text_object->getMarkDown());
    }
    $ret = '';
    if($this->has_children) {
      foreach($this->children as $block) {
        $ret .= sprintf("> %s\n", $block->toMarkDown());
      }
    }
    return sprintf("> %s \n%s", $this->icon, $ret);
  }

  public function toHtml($container = 'div') {
    $ret = $ret = $this->text_object->getHtml();
    if($this->has_children) {
      foreach($this->children as $block) {
        $ret .= sprintf("%s\n", $block->toHtml());
      }
    }
    return sprintf("<div class='callout' style='background-color:%s;border-radius:20px;padding:20px;margin-bottom:10px'>%s&nbsp;&nbsp;%s\n</div>\n",
      Color::getHex($this->color),
      $this->icon,
      $ret
    );
  }
}
