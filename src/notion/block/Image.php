<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;
use b3co\notion\block\interfaces\Uploadable;

class Image extends UploadableBlock implements BlockInterface {

  public $caption;
  public $simple_caption;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);

    $type = '';
    if($data['object'] == 'page') {
      $type = 'cover';
    } else {
      $type = 'image';
    }

    $this->caption = $this->getCaption($data[$type]['caption']);
    $this->simple_caption = "";
    if($this->caption !== null) {
      $this->simple_caption = $this->caption->getPlainText();
    }

    $this->type = $type;
    $this->upload();
  }

  public function toHtml($container = 'div') {
    if($this->caption->plain_text != "" && $this->hasTemplate('html')) {
      return $this->renderTemplate('html');
    } elseif($this->caption->plain_text == "" &&
        $this->hasTemplate('html', 'image_no_caption')) {
      return $this->renderTemplate('html', 'image_no_caption');
    }

    $ret = sprintf("<figure><img src='%s'></figure>\n",
      $this->url
    );

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
}
