<?php

namespace b3co\notion;

use yii\base\BaseObject;

class YiiNotion extends BaseObject {

  public $config;

  public function getNotion() {
    return new Notion($this->config);
  }
}
