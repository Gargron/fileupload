<?php
/**
 * Created by PhpStorm.
 * User: decola
 * Date: 11.07.14
 * Time: 01:43
 */

namespace FileUpload;

class Util
{

    /**
     * Convert human readable file size (e.g. "10K" or "3M") into bytes
     *
     * @param string $input
     * @return int
     */
    public static function humanReadableToBytes($input)
    {
        $number = (int)$input;
        $units = array(
            'b' => 1,
            'k' => 1024,
            'm' => 1048576,
            'g' => 1073741824
        );
        $unit = strtolower(substr($input, -1));
        if (isset($units[$unit])) {
            return ($number * $units[$unit]);
        } else {
            return (null);
        }
    }

    /**
     * Ensure correct value for big integers
     * @param  integer $int
     * @return float
     */

    public static function fixIntegerOverflow($int)
    {

        if ($int < 0) {
            $int += 2.0 * (PHP_INT_MAX + 1);
        }

        return $int;
    }
}
