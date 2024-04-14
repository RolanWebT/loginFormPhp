<?php
namespace Test\Database\Traits;

trait Table {
    private $str_table;
    
    /**
    *    Set the str_table value
    *    @param string $str_table
    */
    public function table($str_table)
    {
        $this->str_table = $str_table;
        return $this;
    }
    
    /**
    *    Returns the str_table value.
    *    @return string
    */
    protected function getTable()
    {
        return $this->str_table;
    }
}