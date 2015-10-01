<?php

namespace App;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Router factory.
 */
class RouterFactory
{
    /**
     * @return \Nette\Application\IRouter
     */
    public static function createRouter()
    {
        $router = new RouteList();
        $router[] = new Route('/docs[</action>]', array(
            'presenter' => 'Homepage',
            'action' => 'docs'
        ));
        $router[] = new Route('/[<presenter>][/<action>][/<id>]', array(
            'presenter' => 'Homepage',
            'action' => 'default',
            'id' => NULL
        ));

        return $router;
    }
}
