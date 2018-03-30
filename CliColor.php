<?php
/**
 * Created by PhpStorm.
 * User: gio
 * Date: 10/13/17
 * Time: 3:17 PM
 */
namespace apollo11\cliLogger;


class CliColor
{
    const F_BLACK = '0;30';
    const F_DARK_GREY = '1;30';
    const F_BLUE = '0;34';
    const F_LIGHT_BLUE = '1;34';
    const F_GREEN = '0;32';
    const F_LIGHT_GREEN = '1;32';
    const F_CYAN = '0;36';
    const F_LIGHT_CYAN = '1;36';
    const F_RED = '0;31';
    const F_LIGHT_RED = '1;31';
    const F_PURPLE = '0;35';
    const F_LIGHT_PURPLE = '1;35';
    const F_BROWN = '0;33';
    const F_YELLOW = '1;33';
    const F_LIGHT_GRAY = '0;37';
    const F_WHITE = '1;37';

    const B_BlACK = '40';
    const B_RED = '41';
    const B_GREEN = '42';
    const B_YELLOW = '43';
    const B_BLUE = '44';
    const B_MAGENTA = '45';
    const B_CYAN = '46';
    const B_LIGHT_GRAY = '47';

    // Returns colored string
    public static function getColoredString($string, $foregroundColor = null, $backgroundColor = null) {
        // Set foreground color and background color
        $coloredString = "";

        if($foregroundColor !== null){
            $coloredString .= "\033[" . $foregroundColor . "m";
        }
        if($backgroundColor !== null) {
            $coloredString .= "\033[" . $backgroundColor . "m";
        }

        $coloredString .=  $string . "\033[0m";

        return $coloredString;
    }


}