<?php

namespace PaulB\RobinHood\Destination;

use geoPHP\Geometry\Point;
use geoPHP\Geometry\Polygon;
use geoPHP\geoPHP;

class Manager 
{
    protected $root;
    protected $children = array();
    protected $all = array();
    
    public function __construct($raw)
    {
        foreach ($raw as $code => $dest) {
            $dest = array_merge(array('code' => $code), $dest);
            $this->all[$code] = $dest;
            if (!empty($dest['is_root']) && true === $dest['is_root']) {
                $this->root = $dest;
            } else {
                $this->children[$code] = $dest;
            }
        }
    }
    
    public function find($code)
    {
        if ($this->root['code'] === $code) {
            return $this->getRoot();
        } 
        
        return $this->getChild($code);
    }
    
    public function getChild($code) 
    {
        return isset($this->children[$code]) ? $this->children[$code] : false;
    }
    
    public function getRoot()
    {
        return $this->root;
    }
    
    public function getChildren()
    {
        return $this->children;
    }
    
    public function getAll()
    {
        return $this->all;
    }
    
    public function getOther($code)
    {
        $all = array();
        
        foreach ($this->all as $dest) {
            if ($dest['code'] !== $code) {
                $all[$dest['code']] = $dest;
            }
        }

        return $all;
    }
    
    public function geocode($lng, $lat)
    {
        $point = new Point($lat, $lng);
        
        foreach ($this->getChildren() as $child) {
            $bounds = sprintf('{ "type": "Polygon", "coordinates": %s }', $child['api_params']['bounds']);
            $poly = geoPHP::load($bounds);
            
            if ($poly->pointInPolygon($point)) {
                return $child;
            }
        }
        
        return $this->getRoot();
    }
}
