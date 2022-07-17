<?php

require_once('objects/inc.php');


use b3co\notion\Page;

define('ENV', getenv('env'));
define('VERBOSE', ENV != 'prod');

if(VERBOSE) printf("ENV: %s\nVERBOSE: %s\n", ENV, VERBOSE);


//$p = new Page('35ad08cba15545f599156488d1368bbc');
//$p = new Page('5eaa6de57317415e8623523950bdf1a7');
$p = new Page('3bca0a57417c49159f464674e2c69754');

echo $p->toMarkDown();
echo "\n";
