<?php

namespace b3co\notion\block;

require_once("BlockInterface.php");

class Image extends Block implements BlockInterface {

  public $caption;
  public $url;

  private $notion_url;

  public function __construct($data) {
    parent::__construct($data);
    $this->caption = $this->getCaption($data['image']['caption']);
    $this->notion_url = $data['image']['file']['url'];
  }

  //TODO
  private function uploadToS3() {

  }

  public function toString() {
    return sprintf("%s\n", $this->notion_url);
  }

  public function toMarkDown() {
    $ret = sprintf("![inline](%s)", $this->notion_url);
    if($this->caption != '') {
      $ret .= sprintf("\n> %s", $this->caption);
    }
    return $ret;
  }

  private function getCaption($data) {
    if(isset($data[0]['text']['content'])) {
      return $data[0]['text']['content'];
    }
    return '';
  }
}
