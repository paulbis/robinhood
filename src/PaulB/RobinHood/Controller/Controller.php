<?php

namespace PaulB\RobinHood\Controller;

abstract class Controller
{
    protected $container;
    
    public function __construct($container)
    {
        $this->container = $container;
    }
}
