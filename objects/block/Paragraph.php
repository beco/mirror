<?php

namespace b3co\notion\block;

require_once("BlockInterface.php");

class Paragraph extends Block implements BlockInterface {

  public $plain_text;
  private $text_object;

  public function __construct($data) {
    parent::__construct($data);
    $this->text_object = $data['paragraph']['rich_text'];
    $this->plain_text  = $this->constructText($this->text_object);
  }

  public function toString() {
    return sprintf("%s\n", $this->plain_text);
  }

  public function toMarkDown() {
    $ret = '';
    //print_r($this->text_object);
    foreach($this->text_object as $o) {
      $rt  = $o['text']['content'];
      $lsp = preg_match('/^\s+.*$/', $rt);
      $rsp = preg_match('/^.*\s+$/', $rt);
      $r   = trim($rt);

      if(is_array($o['text']['link'])) {
        $r = sprintf("[%s](%s)", $r, $o['text']['link']['url']);
      }
      if($o['annotations']['bold']) {
        $r = sprintf("**%s**", $r);
      }
      if($o['annotations']['italic']) {
        $r = sprintf("_%s_", $r);
      }
      if($o['annotations']['strikethrough']) {
        $r = sprintf("~%s~", $r);
      }
      if($o['annotations']['underline']) {
        $r = sprintf("<u>%s</u>", $r);
      }
      if($o['annotations']['code']) {
        $r = sprintf("`%s`", $r);
      }
      $ret .= sprintf("%s%s%s", $lsp?" ":"", $r, $rsp?" ":"");
    }
    return $ret;
  }

  private function constructText($data) {
    $ret = '';
    foreach($data as $element) {
      $ret .= $element['plain_text'];
    }
    return $ret;
  }
}
