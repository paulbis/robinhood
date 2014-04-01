<?php

namespace PaulB\RobinHood\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use PaulB\RobinHood\Destination\Manager as DestinationManager;
use PaulB\RobinHood\City\Manager as CityManager;
use PaulB\RobinHood\Client\Client;
use PaulB\RobinHood\Synchronizer\Synchronizer;

use PaulB\RobinHood\Controller\HomepageController;
use PaulB\RobinHood\Controller\DestinationController;
use PaulB\RobinHood\Controller\CityController;
use PaulB\RobinHood\Controller\OfferController;

class RobinHoodServiceProvider implements ServiceProviderInterface
{
    private $filename;
    
    public function __construct($filename)
    {
        $this->filename = $filename;
    }
    
    public function register(Application $app)
    {
        $raw = json_decode(file_get_contents($this->filename), true);
        
        $app['destinations'] = $app->share(function () use ($app, $raw) {
            return new DestinationManager($raw);
        });
        
        $app['cities'] = $app->share(function () use ($app) {
            return new CityManager($app);
        });
        
        $app['client'] = $app->share(function() use ($app) {
            return new Client($app['api']['params'], $app);
        });
        
        $app['synchronizer'] = $app->share(function() use ($app) {
            return new Synchronizer($app);
        });
        
        $app['homepage.controller'] = $app->share(function() use ($app) {
            return new HomepageController($app);
        });
        $app['destination.controller'] = $app->share(function() use ($app) {
            return new DestinationController($app);
        });
        $app['city.controller'] = $app->share(function() use ($app) {
            return new CityController($app);
        });
        $app['offer.controller'] = $app->share(function() use ($app) {
            return new OfferController($app);
        });
    }

    public function boot(Application $app)
    {
        
    }
}
