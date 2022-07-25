<?php

require 'vendor/autoload.php';
require 'app/autoload.php';


use b3co\notion\Notion;
use b3co\notion\block\Page;

define('ENV', getenv('env'));
define('VERBOSE', ENV != 'prod');

$config = require_once("app/config.php");

if(VERBOSE) printf("ENV: %s\nVERBOSE: on\n", ENV);

$pid = $argv[1];

if(!preg_match('/[a-z0-9]{32}/', $pid)) {
  die("🚨 bad pid\n");
}

$t = 'basic';

$n = new Notion($config);

$p = $n->getPage($pid);
echo $p->toTemplate($t);

echo "------------------------\n";
echo "Cool, 👋\n";
