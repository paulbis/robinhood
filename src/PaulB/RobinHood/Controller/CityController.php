<?php

namespace PaulB\RobinHood\Controller;

class CityController extends Controller
{
    public function listAction()
    {
        $this->container['synchronizer']
                ->synchronizeAllCities();
        
        $cities = $this->container['cities']
                ->getAll();

        return $this->container['twig']->render(
            'cities.twig', array(
                'destinations' => $this->container['destinations']->getChildren(),
                'cities' => $cities,
            )
        );
    }
    
    public function detailsAction()
    {
        $city = $this->container['synchronizer']
                ->findCity($this->container['request']->get('city_id'));

        $offers = $this->container['cities']
                ->getOffers($city);
        
        return $this->container['twig']->render(
            'city.twig', array(
                'destinations' => $this->container['destinations']->getChildren(),
                'city' => $city,
                'offers' => $offers,
            )
        );
    }
    
    public function popularCitiesAction()
    {
        $cities = array_slice(
                $this->container['cities']
                    ->getAll(),
                0,
                $this->container['request']->get('limit', 12)
            );

        return $this->container['twig']->render(
            '_popularCities.twig', array(
                'cities' => $cities,
            )
        );
    }
}
