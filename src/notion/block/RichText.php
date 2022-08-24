<?php

namespace b3co\notion\block;

class RichText {

  public $text_object;
  public $platin_text = '';
  public $html = '';
  public $mark_down = '';

  public function __construct($text_object) {
    $this->text_object = $text_object;
    $this->plain_text  = $this->getPlainText();
    $this->html        = $this->getHtml();
    $this->mark_down   = $this->getMarkDown();
  }

  public function isEmpty() {
    return $this->getPlainText() === '';
  }

  public function getPlainText() {
    $ret = '';
    foreach($this->text_object as $o) {
      if($o['type'] == 'text') {
        $ret .= $o['text']['content'];
      } elseif($o['type'] == 'mention') {
        $ret .= $o['plain_text'];
      }
    }
    return $ret;
  }

  public function getHtml() {
    $ret = '';
    foreach($this->text_object as $o) {
      $rt  = '';

      if($o['type'] == 'text') {
        $rt = $o['text']['content'];
      } elseif($o['type'] == 'mention') {
        $rt = $o['plain_text'];
      }

      $lsp = preg_match('/^\s+.*$/', $rt);
      $rsp = preg_match('/^.*\s+$/', $rt);
      $r   = trim($rt);

      if(is_array($o['text']['link'])) {
        $r = sprintf("<a href='%s'>%s</a>", $o['text']['link']['url'], $r);
      }
      if($o['annotations']['bold']) {
        $r = sprintf("<b>%s</b>", $r);
      }
      if($o['annotations']['italic']) {
        $r = sprintf("<i>%s</i>", $r);
      }
      if($o['annotations']['strikethrough']) {
        $r = sprintf("<strikethrough>%s</strikethrough>", $r);
      }
      if($o['annotations']['underline']) {
        $r = sprintf("<u>%s</u>", $r);
      }
      if($o['annotations']['code']) {
        $r = sprintf("<code>%s</code>", $r);
      }
      $ret .= sprintf("%s%s%s", $lsp?" ":"", $r, $rsp?" ":"");
    }
    return $ret;
  }

  public function getMarkDown() {
    $ret = '';
    foreach($this->text_object as $o) {
      $rt  = '';

      if($o['type'] == 'text') {
        $rt = $o['text']['content'];
      } elseif($o['type'] == 'mention') {
        $rt = $o['plain_text'];
      }

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
}
