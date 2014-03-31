<?php

namespace PaulB\RobinHood\Application;

use Silex\Application as BaseApplication;

use Igorw\Silex\ConfigServiceProvider;
use SilexMongo\MongoDbExtension;
use Silex\Provider\TwigServiceProvider;
use PaulB\RobinHood\Provider\RobinHoodServiceProvider;

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
    }
}
