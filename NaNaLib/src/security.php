<?php

class Common_Security
{

    private static function core($str, $salt)
    {
        $result = '';
        $strLen = strlen($str);
        $saltLen = strlen($salt);

        for ($i = 0; $i < $strLen; $i++) {
            $mod = $i % $saltLen;
            $result.=$str[$i] ^ $salt[$mod];
        }

        return $result;
    }

    public static function encrypt($str,$salt)
    {
        $base64 = base64_encode($str);
        return base64_encode(self::core($base64,$salt));
    }

    public static function decrypt($base64,$salt)
    {
        $str = self::core(base64_decode($base64),$salt);
        return base64_decode($str);
    }
}