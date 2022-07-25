<?php

namespace b3co\notion\block;

use b3co\notion\Notion;

class Block {
  public $id;
  public $type;

  public $has_children;
  public $children;
  public $parent_page;
  public $notion;

  private $raw;

  protected static $html_containers = [
    'div'  => '<div class="element">%s</div>',
    'none' => '%s',
  ];

  public function __construct($data, $parent, $upload = false) {
    $this->id           = $data['id'];
    $this->type         = $data['type'];
    $this->has_children = $data['has_children'];
    $this->raw          = $data;
    $this->parent_page  = $parent;

    if(VERBOSE) printf("initializing %s\n", $this->type);

    if($this->has_children == true) {
      $this->children = $this->parent_page->notion->getChildren($this->id, $parent, $upload);
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

  public function getTemplate($template) {
    $file = sprintf("templates/%s/%s.template",
      $template,
      $this->type
    );
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
            $this->$key, $tmp);
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
}
