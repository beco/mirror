<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;
use b3co\notion\block\interfaces\Uploadable;

class Image extends UploadableBlock implements BlockInterface {

  public $caption;
  public $format;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);

    $type = '';
    if($data['object'] == 'page') {
      $type = 'cover';
    } else {
      $type = 'image';
    }

    $this->caption = $this->getCaption($data[$type]['caption']);
    $this->type    = $type;

    $this->upload();
  }

  public function toHtml($container = 'div') {
    $ret = sprintf("<figure><a href='%s' target='_blank'><img src='%s'></a>",
      $this->url,
      $this->url
    );
    //var_dump($this->caption);
    if(!$this->caption->isEmpty()) {
      $ret .= sprintf("<figcaption style='font-size: 90%%;background-color: #eee; border-radius: 3px;'>👆 %s</figcaption>",
        $this->caption->getHtml());
    }
    $ret .= "</figure>\n";
    return sprintf(Block::$html_containers[$container], $ret);
  }

  public function toString() {
    return sprintf("%s\n", $this->url);
  }

  public function toMarkDown() {
    $ret = sprintf("![inline](%s)", $this->url);
    if($this->caption != '') {
      $ret .= sprintf("\n> %s\n", $this->caption->getMarkDown());
    }
    return $ret;
  }

  private function getCaption($data) {
    if(isset($data)) {
      $ret = new RichText($data);
      return $ret;
    }
    return null;
  }
}
