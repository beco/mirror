<?php

require 'objects/inc.php';
require 'vendor/autoload.php';

use b3co\notion\block\Page;

define('ENV', getenv('env'));
define('VERBOSE', ENV != 'prod');

if(VERBOSE) printf("ENV: %s\nVERBOSE: on\n", ENV);

$t = [];
$t[] = time();
//$p = new Page('35ad08cba15545f599156488d1368bbc'); //tapalehui
//$p = new Page('5eaa6de57317415e8623523950bdf1a7', true); // cafe
//$p = new Page('3bca0a57417c49159f464674e2c69754', true); //examples
//$p = new Page('41058899032d44ce8909014f854bca8a'); //to-dos
$p = new Page('f91c9ccbc90749d0b8232688b1e428df', false); //andrés y ari
//$p = new Page('9c82f3af98e44cf688e8b1130325e3c5', true); //fecha
//$p = new Page('cfe325af60034cb4888e0169b19ced3e'); //columnas
//$p = new Page('51e71a511ad6455895c0b2e5cfbcc48e'); //region 4


echo $p->toHtml();
$t[] = time();
printf("time elapsed: %ds\n", $t[count($t)-1] - $t[0]);
