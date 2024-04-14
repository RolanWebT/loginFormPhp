<?php
namespace Test\Database\Traits;

trait Set {
    private $arr_set;
    
    /**
     * Add value to arr_set
     * @param string $str_col
     * @param string $str_value
     */
    public function set($str_col, $str_value)
    {
        $this->arr_set[$str_col] = $str_value;
        return $this;
    }
    
    /**
    *    Returns the arr_set value.
    *    @return array
    */
    protected function getSet()
    {
        return $this->arr_set;
    }
}