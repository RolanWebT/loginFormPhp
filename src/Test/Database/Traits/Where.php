<?php
namespace Test\Database\Traits;

trait Where {
    private $arr_where=[];
    
    /**
     *    add value to arr_where
     *    @param string $str_where
     */
    public function where($str_col, $str_value)
    {
        $this->arr_where[] = ['and' => [$str_col => $str_value]];
        return $this;
    }
    
    /**
     *    add value to arr_where
     *    @param string $str_where
     */
    public function orWhere($str_col, $str_value)
    {
        $this->arr_where[] = ['or' => [$str_col => $str_value]];
        return $this;
    }
    
    
    /**
     *    Returns the arr_where value.
     *    @return array
     */
    protected function getWhere()
    {
        return $this->arr_where;
    }
}