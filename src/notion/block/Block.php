<?php

namespace b3co\notion\block;

use b3co\notion\Notion;

class Block {
  public $id;
  public $type;

  public $has_children;
  public $children = [];
  public $parent_page;
  public $notion;
  public $upload;

  public $created_at;
  public $updated_at;

  public $is_first = true;
  public $is_last  = true;

  protected $raw;

  protected static $html_containers = [
    'div'  => '<div class="element">%s</div>',
    'none' => '%s',
  ];

  public function __construct($data, $parent, $follow = true) {
    $this->id           = $data['id'];
    $this->type         = $data['type'];
    $this->has_children = $data['has_children'];
    $this->raw          = $data;
    $this->parent_page  = $parent;
    $this->upload       = $parent->upload;

    $this->created_at   = $data['created_time'];
    $this->updated_at   = $data['last_edited_time'];

    if($this->has_children == true && $follow) {
      $this->children = $this->parent_page->notion
        ->getChildren($this->id, $parent);
    }

    if($data['parent']['type'] == 'page_id') {
      $this->parent_page_id = $data['parent']['page_id'];
    }
  }

  public function getChildrenByType($type) {
    $ret = [];
    foreach($this->children as $child) {
      if($child->type == $type) {
        $ret[] = $child;
      }
    }
    return $ret;
  }

  public function getRaw() {
    return $this->raw;
  }

  public function isPage() {
    return $this->type == 'page';
  }

  public function hasTemplate($template, $file = null):bool {
    $exists = file_exists($this->getTemplateFileName($template, $file));
    if(VERBOSE)
      fwrite(STDERR,
        sprintf("> %s %s %s\n",
          $this->getTemplateFileName($template, $file),
          $file,
          $exists?"✅":"❌"
        )
      );
    return $exists;
  }

  public function getTemplateFileName($template, $file = null):string {
    return sprintf("templates/%s/%s.template",
      $template,
      $file === null?$this->type:$file
    );
  }

  public function getTemplate($template, $file = null) {
    $file = $this->getTemplateFileName($template, $file);
    if(file_exists($file)) {
      return file_get_contents($file);
    }
    return '';
  }

  public function renderTemplate($template, $file = null):string {
    $tmp  = $this->getTemplate($template, $file);
    $vars = [];
    if(preg_match_all('/\[:([a-z0_9\_]+)\]/', $tmp, $m)) {
      foreach($m[1] as $key) {
        if(!isset($vars[$key])) {
          if($this->$key != null) {
            $vars[$key] = $this->$key;
          } else {
            $vars[$key] = "";
          }

          $tmp = preg_replace(
            sprintf('/\[:%s\]/', $key),
            preg_replace('/\$/', '\\\$', $this->$key),
            $tmp);
        }
      }
      return $tmp;
    }
    return '';
  }

  public function toTemplate($template, $file = null) {
    if($this->getTemplate($template, $file) !== '') {
      return $this->renderTemplate($template, $file);
    }
    if(VERBOSE) printf("No template for %s\n", $this->type);
    return $this->toHtml();
  }

  public function getChildrenBody($template = 'html', $file = null) {
    if(count($this->children) == 0) {
      return '';
    }
    $ret = '';
    foreach($this->children as $block) {
      if($template === 'html' && $file === null) {
        $ret .= $block->toHtml();
      } else {
        $ret .= $block->renderTemplate($template, $file);
      }
    }
    return $ret;
  }

  protected function getCaption($data) {
    if(isset($data)) {
      $ret = new RichText($data);
      return $ret;
    }
    return null;
  }
}
