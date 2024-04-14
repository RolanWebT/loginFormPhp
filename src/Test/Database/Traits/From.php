<?php
namespace Test\Database\Traits;

trait From {
    private $str_from = '';
    
    /**
    *    Set the str_from value
    *    @param string $str_from
    */
    public function from($str_from)
    {
        $this->str_from = $str_from;
        return $this;
    }
    
    /**
     * Get the str_from value
     * @return string
     */
    protected function getFrom()
    {
        return $this->str_from;
    }
}