<?php
namespace Test\Database;
abstract class Query {
    
    public function __get($str_name)
    {
        switch(strtolower($str_name)){
            case '__from':
                return $this->getFrom();
            case '__table':
                return $this->getTable();
            case '__where':
                return $this->getWhere();
            case '__set':
                return $this->getSet();
            case '__cols':
                return $this->getCols();
        }
    }
}