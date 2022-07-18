<?php

namespace b3co\notion\block;

use b3co\notion\Notion;

class Block {
  public $id;
  public $type;

  public $has_children;
  public $children;
  public $parent_page_id;

  private $raw;
  private $notion;

  protected static $html_containers = [
    'div'  => '<div class="element">%s</div>',
    'none' => '%s',
  ];

  public function __construct($data, $upload = false) {
    $this->id           = $data['id'];
    $this->type         = $data['type'];
    $this->has_children = $data['has_children'];
    $this->raw          = $data;

    if(VERBOSE) printf("initializing %s\n", $this->type);
    
    if($this->has_children == true) {
      $this->notion = new Notion();
      $this->children = $this->notion->getChildren($this->id, $upload);
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
}
