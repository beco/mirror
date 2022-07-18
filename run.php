<?php

require 'objects/inc.php';
require 'vendor/autoload.php';

use b3co\notion\Page;

define('ENV', getenv('env'));
define('VERBOSE', ENV != 'prod');

if(VERBOSE) printf("ENV: %s\nVERBOSE: %s\n", ENV, VERBOSE);

$t = [];
$t[] = time();
//$p = new Page('35ad08cba15545f599156488d1368bbc');
$p = new Page('5eaa6de57317415e8623523950bdf1a7', true);
//$p = new Page('3bca0a57417c49159f464674e2c69754'); //examples
//$p = new Page('41058899032d44ce8909014f854bca8a'); //to-dos
//$p = new Page('f91c9ccbc90749d0b8232688b1e428df', true); //andrés y ari
//$p = new Page('9c82f3af98e44cf688e8b1130325e3c5', true); //fecha


echo $p->toMarkDown();
//echo "\n";
//echo $p->toString();
$t[] = time();
printf("time elapsed: %d", $t[count($t)-1] - $t[0]);
