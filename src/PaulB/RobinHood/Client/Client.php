<?php

namespace PaulB\RobinHood\Client;

use Guzzle\Http\Client as HttpClient;
use PaulB\RobinHood\Exception\Exception;
use Guzzle\Http\Exception\ClientErrorResponseException;

class Client
{
    private $httpClient;
    private $baseParams;
    private $container;
    
    public function __construct($baseParams, $container)
    {
        $this->baseParams = $baseParams;
        $this->container = $container;
    }
    
    public function getRooms($params)
    {
        $results = $this->get('/rooms/', $params);
        
        foreach ($results['results'] as $i => $offer) {
            $results['results'][$i] = $this->container['synchronizer']
                    ->synchronizeOffer($offer);
        }
        
        return $results;
    }
    
    public function getRoom($slug)
    {
        $offer = $this->container['synchronizer']
                ->findBySlug($slug);
        
        if (!empty($offer)) {
            return array_merge($offer, $this->get('/rooms/' . $offer['_id']));
        }
        
        return false;
    }
    
    private function getHttpClient()
    {
        if (!$this->httpClient instanceof HttpClient) {
            $this->httpClient = new HttpClient($this->container['api']['url'], array(
                'user-agent' => $this->container['api']['user_agent'],
            ));
        }
        
        return $this->httpClient;
    }
    
    private function get($endpoint, $params = array())
    {
        try {
            $response = $this->getHttpClient()
                    ->get($this->getURI($endpoint, $params))
                    ->send();
        } catch (ClientErrorResponseException $e) {
            preg_match('`(?<=\[status code\] )[\d]+`', $e->getMessage(), $status);
            preg_match('`(?<=\[reason phrase\] )(.*)`', $e->getMessage(), $reason);

            throw new Exception(current($reason), current($status));
        }
        
        return json_decode($response->getBody(true), true);
    }
    
    private function getURI($endpoint, $params)
    {
        return sprintf('%s?%s', $endpoint, http_build_query(
                array_merge(
                    $this->baseParams, 
                    $params
                )
        ));
    }
}
