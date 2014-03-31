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
    
    public function synchronize()
    {
        $root = $this->container['destinations']
                ->getRoot();
        
        $params = array_merge(array(
            'per_page' => 0,
            'page' => 1,
        ), $root['api_params']);
        $base = $this->container['client']->getRooms($params);
        
        $params['per_page'] = self::TRESHOLD;
        $pages = ceil($base['totalResults'] / self::TRESHOLD);
        
        for ($i = 1; $i <= $pages; $i++) {
            $params['page'] = $i;
            $offers = $this->container['client']->getRooms($params);
            
            foreach ($offers['results'] as $offer) {
                $this->synchronizeOffer($offer);
            }
        }
    }
    
    public function synchronizeOffer($offer) 
    {
        $exists = $this->findById($offer['id']);
        
        if (!$exists) {
            $baseSlug = $slug = Inflector::urlize(sprintf('%s-%s', $offer['city'], $offer['title']));
            $n = 1;

            while ($this->findBySlug($slug)) {
                $n++;
                $slug = $baseSlug . '-' . $n;
            }

            $this->save($offer['id'], $slug, $offer['city']);
            
            return array_merge(array('slug' => $slug), $offer);
        }
        
        return array_merge($exists, $offer);
    }
    
    public function findBySlug($slug)
    {
        return $this->getMongoCollection()
                ->findOne(array(
                    'slug' => $slug,
                ));
    }
    
    public function findById($id)
    {
        return $this->getMongoCollection()
                ->findOne(array(
                    '_id' => $id,
                ));
    }
    
    private function save($id, $slug, $city)
    {
        $doc = array(
            '_id' => $id,
            'slug' => $slug,
            'city' => $city,
        );
        
        return $this->getMongoCollection()
                ->insert($doc);
    }
    
    private function getMongoCollection()
    {
        return $this->container['mongodb']
                ->selectCollection($this->container['mongo']['dbname'], 'offers');
    }
}