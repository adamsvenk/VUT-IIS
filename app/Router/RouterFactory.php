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

		$router->addRoute('/health-problems/list', 'HealthProblem:list');

		$router->addRoute('/reports/<healthProblemId>/list', 'Report:list');
		$router->addRoute('/reports/<healthProblemId>/create', 'Report:create');
        $router->addRoute('/reports/<healthProblemId>/edit/<reportId>', 'Report:edit');
        $router->addRoute('/reports/<healthProblemId>/delete/<reportId>', 'Report:delete');
		$router->addRoute('/reports/<healthProblemId>/detail/<reportId>', 'Report:detail');
		$router->addRoute('/reports/image/<reportId>', 'Report:image');

        $router->addRoute('/examinations/<healthProblemId>/list', 'Examination:list');
        $router->addRoute('/examinations/<healthProblemId>/create', 'Examination:create');
        $router->addRoute('/examinations/<healthProblemId>/edit/<examinationId>', 'Examination:edit');
        $router->addRoute('/examinations/<healthProblemId>/delete/<examinationId>', 'Examination:delete');

		$router->addRoute('<presenter>/<action>', 'Homepage:default');
		return $router;
	}
}
