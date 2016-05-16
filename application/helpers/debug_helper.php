<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Debug
{
    /**
     * 修正 print_r 無法正常顯示布林值的問題
     * 直接把布林值取代成字串
     * @author Leo.Kuo
     */
    public static function print_r_fix($value, $flag=FALSE)
    {
        $fixed_value = self::fix_print_r_boolean($value);
        if ($flag)
            return print_r($fixed_value, $flag);
        else
            print_r($fixed_value);
    }

    public static function fix_print_r_boolean($value)
    {
        if (is_array($value))
        {
            foreach ($value as $key => $v)
            {
                $value[$key] = self::fix_print_r_boolean($v);
            }
        }
        else if (is_object($value))
        {

            foreach ($value as $key => $v)
            {
                $value->$key = self::fix_print_r_boolean($v);
            }
        }
        else if (gettype($value) == "boolean")
        {
            $flag = ($value) ? "True" : "False";
            $value = "$flag (Boolean)";
        }

        return $value;
    }
}