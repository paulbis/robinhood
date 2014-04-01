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
        $cities = array();

        $raw = $this->getMongoCollection()
                ->group(array(
                    'city' => 1,
                ), array(
                    'count' => 0,
                ), 'function (obj, prev) { prev.count++; }');
        
        foreach ($raw as $city) {
            if (!empty($city['city'])) {
                $cities[$city['city']] = $city['count'];
            }
        }
        
        arsort($cities);
        
        return $cities;
    }
    
    private function getMongoCollection()
    {
        return $this->container['mongodb']
                ->selectCollection($this->container['mongo']['dbname'], 'offers');
    }
}
