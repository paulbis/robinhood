<?php

namespace PaulB\RobinHood\Synchronizer;

use PaulB\RobinHood\Util\Inflector;

class Synchronizer
{
    const TRESHOLD = 100;
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
    
    public function synchronizeOffer($offer) 
    {
        $exists = $this->findOfferById($offer['id']);
        
        if (!$exists) {
            $baseSlug = $slug = Inflector::urlize($offer['title']);
            $n = 1;

            while ($this->findOfferBySlug($slug)) {
                $n++;
                $slug = $baseSlug . '-' . $n;
            }
            
            if (!empty($offer['loc']) && !empty($offer['loc']['lat']) && !empty($offer['loc']['lng'])) {
                $destination = $this->container['destinations']
                        ->geocode($offer['loc']['lat'], $offer['loc']['lng']);
            } else {
                $destination = $this->container['destinations']->getRoot();
            }
            
            $this->saveOffer($offer['id'], $slug, $offer['city'], $destination['code']);

            return array_merge(array('slug' => $slug, 'destination_id' => $destination['code']), $offer);
        }
        
        return array_merge($exists, $offer);
    }
    
    public function findOfferBySlug($slug)
    {
        return $this->getOffersMongoCollection()
                ->findOne(array(
                    'slug' => $slug,
                ));
    }
    
    public function findOfferById($id)
    {
        return $this->getOffersMongoCollection()
                ->findOne(array(
                    '_id' => $id,
                ));
    }
    
    public function findOfferIdsByCity($city)
    {
        $offers = array();
        $cursor = $this->getOffersMongoCollection()
                ->find(array(
                    'city' => $city,
                ), array(
                    '_id' => true,
                ));
        
        foreach ($cursor as $offer) {
            $offers[] = $offer['_id'];
        }
        
        return $offers;
    }
    
    private function saveOffer($id, $slug, $city, $destination)
    {
        $doc = array(
            '_id' => $id,
            'slug' => $slug,
            'destination_id' => $destination,
            'city' => $city,
        );
        
        return $this->getOffersMongoCollection()
                ->insert($doc);
    }
    
    public function findCity($id)
    {
        return $this->getCitiesMongoCollection()
                ->findOne(array(
                    '_id' => $id,
                ));
    }
    
    public function synchronizeAllCities()
    {
        $cities = array();

        $raw = $this->getOffersMongoCollection()
                ->group(array(
                    'city' => 1,
                ), array(
                    'count' => 0,
                ), 'function (obj, prev) { prev.count++; }');
        
        foreach ($raw as $city) {
            if (!empty($city['city'])) {
                $slug = Inflector::urlize($city['city']);
                $this->getCitiesMongoCollection()
                        ->update(array(
                            '_id' => $slug,
                        ), array(
                            '_id' => $slug,
                            'name' => $city['city'],
                            'count' => $city['count'],
                        ), array(
                            'upsert' => true,
                        ));
            }
        }
        
        return true;
    }

    public function getOffersMongoCollection()
    {
        return $this->getMongoCollection('offers');
    }
    
    public function getCitiesMongoCollection()
    {
        return $this->getMongoCollection('cities');
    }
    
    private function getMongoCollection($collection)
    {
        return $this->container['mongodb']
                ->selectCollection($this->container['mongo']['dbname'], $collection);
    }
}