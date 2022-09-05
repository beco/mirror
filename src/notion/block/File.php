<?php

namespace b3co\notion\block;

use b3co\notion\block\interfaces\BlockInterface;

class File extends UploadableBlock implements BlockInterface {

  public $caption;

  public function __construct($data, $parent) {
    parent::__construct($data, $parent);
    $this->caption = $this->getCaption($data['file']['caption']);
    $this->upload();
  }

  public function toString() {
    return sprintf("%s\n", $this->text_object->getPlainText());
  }

  public function toMarkDown() {
    return sprintf("### %s", $this->text_object->getMarkDown());
  }

  public function toHtml($container = 'div') {
    $ret = sprintf("<div>\n  <p><a href='%s'>File</a></p>\n", $this->getS3Url());
    if($this->caption !== null && !$this->caption->isEmpty()) {
      $ret .= sprintf("  <p>👆%s</p>\n", $this->caption->getHtml());
    }
    $ret .= "</div>";
    return $ret;
  }
}
