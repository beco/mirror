<?php

require 'vendor/autoload.php';
require 'app/autoload.php';

use b3co\notion\Notion;

$config   = require_once("app/config.php");

define('VERBOSE', true);

$notion   = new Notion($config);
$page     = $notion->getPage('1ffd94c40173456d820bf5d947a99127');
$children = $page->getChildrenPages();

foreach($children as $child) {
  printf(" - %s – %s (%s)\n", $child->page->icon, $child->title, $child->page->id);
}

echo "👋\n";
