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

  private $raw;

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

    $this->created_at = $data['created_time'];
    $this->updated_at = $data['last_edited_time'];

    if($this->has_children == true && $follow) {
      $this->children = $this->parent_page->notion
        ->getChildren($this->id, $parent);
    }

    if($data['parent']['type'] == 'page_id') {
      $this->parent_page_id = $data['parent']['page_id'];
    }
  }


  public function getRaw() {
    return $this->raw;
  }

  public function isPage() {
    return $this->type == 'page';
  }

  public function hasTemplate($template):bool {
    if(VERBOSE) printf("> %s\n", $this->getTemplateFileName($template));
    return file_exists($this->getTemplateFileName($template));
  }

  public function getTemplateFileName($template):string {
    return sprintf("templates/%s/%s.template",
      $template,
      $this->type
    );
  }

  public function getTemplate($template) {
    $file = $this->getTemplateFileName($template);
    if(file_exists($file)) {
      return file_get_contents($file);
    }
    return '';
  }

  public function renderTemplate($template):string {
    $tmp  = $this->getTemplate($template);
    $vars = [];
    if(preg_match_all('/\[:([a-z0_9\_]+)\]/', $tmp, $m)) {
      foreach($m[1] as $key) {
        if(!$vars[$key]) {
          $vars[$key] = $this->$key;
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

  public function toTemplate($template) {
    if($this->getTemplate($template) !== '') {
      return $this->renderTemplate($template);
    }
    if(VERBOSE) printf("No template for %s\n", $this->type);
    return $this->toHtml();
  }

  public function getChildrenBody($template = 'html') {
    if(count($this->children) == 0) {
      return '';
    }
    $ret = '';
    foreach($this->children as $block) {
      if($template === 'html') {
        $ret .= $block->toHtml();
      } else {
        $ret .= $block->renderTemplate($template);
      }
      $ret .= "\n";
    }
    return $ret;
  }
}
