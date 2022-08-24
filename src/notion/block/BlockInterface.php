<?php

namespace b3co\notion\block;

interface BlockInterface {
  public function toString();
  public function toMarkDown();
}

interface Uploadable {
  public function isUploaded():bool;
  public function uploadToS3():bool;
  public function getS3Key():string;
}
