<?php

namespace b3co\notion\utils;

class Color {

  private static $colors = [
    'gray_background' => '#f1f1f1',
    'red_background' => '#ffc4c4',
    'green_background' => '#E8F0E7',
    'blue_background' => '#E1F0F6',
    'yellow_background' => '#F9F1D3',
    'orange_background' => '#F9E7D5',
  ];

  public static function getHex($color) {
    if(isset(self::$colors[$color])) {
      return self::$colors[$color];
    }
    return '#ccc';
  }
}
