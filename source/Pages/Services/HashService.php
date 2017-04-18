<?php

namespace Spiral\Pages\Services;

class HashService
{
    /**
     * @param string $hash1
     * @param string $hash2
     * @return bool
     */
    public function compareHashes($hash1, $hash2): bool
    {
        return hash_equals($hash1, $hash2);
    }
}

if (!function_exists('hash_equals')) {
    function hash_equals($str1, $str2)
    {
        if (strlen($str1) != strlen($str2)) {
            return false;
        } else {
            $res = $str1 ^ $str2;
            $ret = 0;
            for ($i = strlen($res) - 1; $i >= 0; $i--) {
                $ret |= ord($res[$i]);
            }

            return !$ret;
        }
    }
}