<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class Bookmark extends Block implements BlockInterface {

  private $url;
  private $caption;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->url     = $data[$this->type]['url'];
    $this->caption = new RichText($data[$this->type]['caption']);
  }

  public function toString() {
    return $this->url;
  }

  public function toMarkDown() {
    return sprintf("[%s](%s)",
      $this->caption == ''?'link':$this->caption->getMarkDown(),
      $this->link
    );
  }

  public function toHtml($container = 'div') {
    return sprintf("🔖 <a href='%s'>%s</a>",
      $this->link,
      $this->caption == ''?'link':$this->caption->getHtml()
    );
  }
}
