<?php

namespace Test\Database;

interface Driver {
    /** fetcher */
    public function fetchRow(\Test\Database\Query $obj_query, array $arr_alias=[]);
    public function fetchCol(\Test\Database\Query $obj_query, array $arr_alias=[]);
    public function fetchOne(\Test\Database\Query $obj_query, array $arr_alias=[]);
    public function fetchAll(\Test\Database\Query $obj_query, array $arr_alias=[]);
    public function fetchAssoc(\Test\Database\Query $obj_query, array $arr_alias=[]);
    public function execute(\Test\Database\Query $obj_query, array $arr_alias=[]);
    
    /** query builder */
    public function buildSelect();
    public function buildDelete();
    public function buildUpdate();
    public function buildInsert();
}