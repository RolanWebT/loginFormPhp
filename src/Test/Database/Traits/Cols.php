<?php
namespace Test\Database\Traits;

trait Cols {
    private $arr_cols=[];
    
    /**
    *    Add value to arr_cols
    *    @param string $str_cols
    */
    public function col($str_cols)
    {
        $this->arr_cols[] = $str_cols;
        return $this;
    }
    
    /**
    *    Set the arr_cols value
    *    @param array $arr_cols
    */
    public function cols($arr_cols)
    {
        $this->arr_cols = $arr_cols;
        return $this;
    }
    
    /**
     * Get the arr_cols value
     * @return array
     */
    protected function getCols()
    {
        return $this->arr_cols;
    }
}