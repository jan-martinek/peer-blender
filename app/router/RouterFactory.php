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
        $router[] = new Route('/docs[/<doc .+\.md>]', array(
            'presenter' => 'Docs',
            'action' => 'default'
        ));
        $router[] = new Route('/[<presenter>][/<action>][/<id>]', array(
            'presenter' => 'Homepage',
            'action' => 'default',
            'id' => NULL
        ));

        return $router;
    }
}
