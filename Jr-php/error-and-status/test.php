<?php
namespace Jr;
class test {
    protected $prop;
    
    function getProp(){
        echo empty($this->prop) ? "empty":'not empty';
    }
}

$o = new test;
$o->getProp();