<?php
namespace Test\Database\Traits;

trait Quote {
    public function quote($msc_value, $str_type='')
    {
        switch(strtolower($str_type)){
            case 'int':
            case 'integer':
                return (int)$msc_value;
            case 'boo':
            case 'bool':
            case 'boolean':
                return (bool)$msc_value;
            case 'float':
            case 'flt':
            case 'dbl':
            case 'double':
            case 'number':
            case 'num':
                return (float)$msc_value;
            case 'string':
            case 'text':
            case 'varchar':
            case 'char':
            case 'str':
            case 'txt':
            case 'dtm':
            case 'date':
            case 'datetime':
            case 'time':
                return addslashes($msc_value);
            default:
                if(is_float($msc_value))
                    return (float)$msc_value;
                if(is_int($msc_value))
                    return (int)$msc_value;
                if(is_bool($msc_value))
                    return (bool)$msc_value;
                if(is_string($msc_value))
                    return addslashes($msc_value);
        }
    }
}