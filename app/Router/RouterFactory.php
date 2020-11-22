<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

		$router->addRoute('sign/in', 'Sign:in');

		$router->addRoute('users/list', 'User:list');
		$router->addRoute('users/delete/<userId>', 'User:delete');
		$router->addRoute('users/create', 'User:create');
		$router->addRoute('users/edit/<userId>', 'User:edit');

		$router->addRoute('<presenter>/<action>', 'Homepage:default');
		return $router;
	}
}
