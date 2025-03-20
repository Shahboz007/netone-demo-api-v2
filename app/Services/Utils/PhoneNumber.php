<?php

namespace App\Services\Utils;

class PhoneNumber
{
  public static function format(string $number, $prefix="+998")
  {
    // Ensure the number is a string for manipulation
    $number = str_pad($number, 9, '0', STR_PAD_LEFT);

    // Extract the parts of the number
    $prefix = substr($number, 0, 2); // First two digits
    $firstPart = substr($number, 2, 3); // Next three digits
    $hiddenPart = '***'; // Masked part
    $lastPart = substr($number, -2); // Last two digits

    // Combine the parts into the desired format
    return "$prefix $prefix $hiddenPart ** $lastPart";
  }
}
