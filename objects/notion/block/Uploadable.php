<?php

namespace b3co\notion\block;

interface Uploadable {
  public function isUploaded():bool;
  public function uploadToS3():bool;
  public function getS3Key():string;
}
