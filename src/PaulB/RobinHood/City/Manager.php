<?php

namespace PaulB\RobinHood\City;

class Manager
{
    protected $container;
    
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    public function getAll()
    {
        $test = $this->getMongoCollection()
                ->group(array(
                    'city' => 1,
                ), array(
                    'items' => array(),
                ), 'reduce');
        
        die(var_dump($test));
    }
    
    private function getMongoCollection()
    {
        return $this->container['mongodb']
                ->selectCollection($this->container['mongo']['dbname'], 'offers');
    }
}
