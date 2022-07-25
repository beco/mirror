<?php

require 'vendor/autoload.php';
require 'app/autoload.php';

use b3co\notion\Notion;
use b3co\notion\block\Page;

define('ENV', getenv('env'));
define('VERBOSE', ENV != 'prod');

$config = require_once("app/config.php");

if(VERBOSE) printf("ENV: %s\nVERBOSE: on\n", ENV);

function readOptions($question, $options, $default = '') {
  do {
    $a = readline(sprintf("%s [%s]: ", $question, join(",", $options)));
  } while(!in_array($a, $options));
  return $a;
}

$c = true;

if(isset($argv[1]) && preg_match('/^\w{32}$/', $argv[1])) {
  $pid = $argv[1];
  $c = false;
} else {
  $pid = readline("page id: ");
}

if(isset($argv[2]) && in_array($argv[2], ['html', 'text', 'md'])) {
  $exp = $argv[2];
} else {
  $exp = readOptions("export format", ['html', 'text', 'md']);
}

if(isset($argv[3]) && in_array($argv[3], ['y', 'n'])) {
  $s3 = $argv[3];
} else {
  $s3 = readOptions("upload to s3?", ['y', 'n']);
}

try {
  $n = new Notion($config);
  $p = $n->getPage($pid, $s3 == 'y'); // <- aquí
  printf("page loaded: %s\n", $p->title);
  switch($exp) {
    case 'html':
      echo $p->toHtml();
      break;
    case 'md':
      echo $p->toMarkDown();
      break;
    case 'text':
      echo $p->toString();
      break;
  }
} catch(Exception $e) {
  printf("[error] no page\n");
  echo $e->getMessage();
} finally {
  $p = null;
}
echo "------------------------\n";

//echo $p->toHtml();
echo "Cool, 👋\n";
