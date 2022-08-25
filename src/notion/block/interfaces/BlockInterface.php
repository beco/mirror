<?php

namespace b3co\notion\block\interfaces;

interface BlockInterface {
  public function toString();
  public function toMarkDown();
  public function toHtml();
}
