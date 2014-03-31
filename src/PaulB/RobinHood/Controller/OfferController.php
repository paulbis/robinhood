<?php

namespace PaulB\RobinHood\Controller;

class OfferController extends Controller
{
    public function detailsAction()
    {
        $offer = $this->getOffer();
        $destination = $this->container['destinations']->geocode($offer['location']['location'][1], $offer['location']['location'][0]);

        return $this->container['twig']->render('details.twig', array(
            'offer' => $offer,
            'destination' => $destination,
            'destinations' => $this->container['destinations']->getChildren(),
        ));
    }
    
    public function redirectAction()
    {
        $offer = $this->getOffer();
        
        return $this->container->redirect($offer['url']);
    }
    
    private function getOffer()
    {
        return $this->container['client']->getRoom($this->container['request']->get('offer_id'));
    }
}
