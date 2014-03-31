<?php

namespace PaulB\RobinHood\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use PaulB\RobinHood\Destination\Manager as DestinationManager;
use PaulB\RobinHood\Client\Client;
use PaulB\RobinHood\Synchronizer\Synchronizer;

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
        
        $app['client'] = $app->share(function() use ($app) {
            return new Client($app['api']['params'], $app);
        });
        
        $app['synchronizer'] = $app->share(function() use ($app) {
            return new Synchronizer($app);
        });
    }

    public function boot(Application $app)
    {
        
    }
}
