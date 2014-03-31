<?php

namespace PaulB\RobinHood\Controller;

class DestinationController extends Controller
{
    public function rootOffersAction()
    {
        $root = $this->container['destinations']->getRoot();
        $page = $this->container['request']->get('page', 1);
        
        return $this->commonOffersAction($root, $this->container['destinations']->getChildren(), $page);
    }
    
    public function destinationOffersAction()
    {
        $destination = $this->container['destinations']->getChild($this->container['request']->get('destination_id'));
        $page = $this->container['request']->get('page', 1);
        
        return $this->commonOffersAction($destination, $this->container['destinations']->getChildren(), $page);
    }
    
    private function commonOffersAction($destination, $destinations, $page, $template)
    {
        return $this->container['twig']->render(
            'destination.twig', array(
                'destination' => $destination,
                'destinations' => $destinations,
                'offers' => $this->container['client']->getRooms(array_merge(array(
                    'page' => (int) $page,
                    'per_page' => 20,
                ), $destination['api_params'])),
            )
        );
    }
}
