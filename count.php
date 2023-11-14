<?php

require 'vendor/autoload.php';
require 'app/autoload.php';

use b3co\notion\Notion;

$config = require_once("app/config.php");

define('ENV', getenv('env'));
define('VERBOSE', ENV != 'prod');

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

try {
  $n = new Notion($config);
  $s = $n->getStats($pid);
  $words = 0;
  foreach($s as $t => $i) {
    printf(" - There %s %d %s%s with %d words and %d chars\n",
      $i['count'] > 1?'are':'is',
      $i['count'],
      $t,
      $i['count'] > 1?'s':'',
      $i['words'],
      $i['chars']
    );
    $words += $i['words'];
  }
  printf("An aprox of %0.2f minutes of reading\n", $words/191);
} catch(Exception $e) {
  printf("[error] no page\n");
  echo $e->getMessage();
} finally {
  $p = null;
}
echo "------------------------\n";

//echo $p->toHtml();
echo "Cool, 👋\n";
