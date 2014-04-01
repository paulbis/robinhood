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

        $raw = $this->container['synchronizer']
                ->getCitiesMongoCollection()
                ->find()
                ->sort(array(
                    'count' => \MongoCollection::DESCENDING,
                ));
        
        foreach ($raw as $city) {
            if (!empty($city['_id'])) {
                $cities[$city['_id']] = array(
                    'slug' => $city['_id'],
                    'name' => $city['name'],
                    'count' => $city['count'],
                );
            }
        }
        
        return $cities;
    }
    
    public function getOffers($city)
    {
        $offerIds = $this->container['synchronizer']
                ->findOfferIdsByCity($city['name']);
        
        $offers = $this->container['client']
                ->getRoomsByIds($offerIds);
        
        return $offers;
    }
}
