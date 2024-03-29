<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class Embed extends Block implements BlockInterface {

  public $url;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->url = $data['embed']['url'];
  }

  public function toString() {
    return sprintf("%[EMBED]: \n", $this->url);
  }

  public function toMarkDown() {
    return $this->toHtml('');
  }

  public function toHtml($container = 'div') {
    return sprintf("<iframe src='%s' style='width:100%%' height='600px'></iframe>", $this->url);
  }
}
