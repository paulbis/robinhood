<?php

namespace PaulB\RobinHood\Application;

use Silex\Application as BaseApplication;

use Igorw\Silex\ConfigServiceProvider;
use SilexMongo\MongoDbExtension;
use Silex\Provider\TwigServiceProvider;
use PaulB\RobinHood\Provider\RobinHoodServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

use PaulB\RobinHood\Controller\HomepageController;
use PaulB\RobinHood\Controller\DestinationController;
use PaulB\RobinHood\Controller\CityController;
use PaulB\RobinHood\Controller\OfferController;

class Application extends BaseApplication
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->register(new ConfigServiceProvider($values['configuration_path']));
        $this->register(new RobinHoodServiceProvider($values['destinations_path']));
        $this->register(new MongoDbExtension(), $this['mongo']);
        $this->register(new TwigServiceProvider(), array(
            'twig.path' => $values['views_path'],
        ));
        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new ServiceControllerServiceProvider());
        
        $app = $this;
        $this['homepage.controller'] = $this->share(function() use ($app) {
            return new HomepageController($app);
        });
        $this['destination.controller'] = $this->share(function() use ($app) {
            return new DestinationController($app);
        });
        $this['city.controller'] = $this->share(function() use ($app) {
            return new CityController($app);
        });
        $this['offer.controller'] = $this->share(function() use ($app) {
            return new OfferController($app);
        });
    }
}
