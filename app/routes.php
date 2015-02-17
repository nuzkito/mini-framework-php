<?php

use Core\View as View;

$router->add('/', function ()
{
	return View::make('home');
});
