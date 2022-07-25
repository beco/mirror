<?php

spl_autoload_register(function ($class_name) {
  $dirs = explode('\\', $class_name);
  $dirs = array_slice($dirs, 1);
  $file = sprintf("objects/%s.php", join($dirs, DIRECTORY_SEPARATOR));
  
  if(file_exists($file)) {
    include $file;
    return true;
  }
  return false;
});
