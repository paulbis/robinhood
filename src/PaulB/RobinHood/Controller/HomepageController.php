<?php

namespace PaulB\RobinHood\Controller;

class HomepageController
{
    protected $container;
    
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    public function homepageAction()
    {
        $destination = $this->container['destinations']
                ->getRoot();

        return $this->container['twig']->render('homepage.twig', array(
            'destination' => $destination,
            'destinations' => $this->container['destinations']->getOther($destination['code']),
            'offers' => $this->container['client']->getRooms(array_merge(array(
                'per_page' => 6,
            ), $destination['api_params'])),
        ));
    }
}
