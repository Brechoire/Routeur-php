<?php 
class Router
{
	private static $_controller;
	private static $_routes 			= [];
	private static $_matches 			= [];
	private static $_ressourcesMatches 	= [];
	private static $_paramMatch 		= [];
	private static $_params     		= [];
	private static $_ressourceRoute;
	
	public static function ressources($route, $actions =[])
	{
		$route = trim($route, '/');
		$url = $_GET['url'];
		$path = preg_replace('#\*#', '([a-z0-9]*)/?', $route);
		$routes = explode('/', $url);
		preg_match('#[a-z]+#i', $path, $controller);
		self::$_ressourceRoute = !empty($routes[0]) ? $routes[0] : 'index';
		self::$_matches = array_slice($routes, 1); // !important : garder
		foreach ($actions as $action) {
			self::$_routes[$action] = $controller[0].'@'.$action;
		}
	}

	public static function matchRessource($regex, $acCtrl)
	{
		if(!preg_match("#$regex#", $acCtrl)){
			return false;
		}else{
			return true;	
		}
	}

	public static function called($controller)
	{
		if (is_string($controller)) {
			require_once 'controllers/BaseController.php';
			$params = explode('@', $controller);
	        $controller = $params[0] . "Controller";
	        if (!file_exists("controllers/".$controller.'.php')) {
	        	return self::called('base@notFound');
	        }
	        require_once "controllers/".$controller.'.php';
	        $controller = new $controller();
	        if (!method_exists($controller, $params[1])) {
	        	echo "methode exite pas dans ce controleur";
	        	return self::called('base@notFound');//si fichier exite pas
	        }
	        return call_user_func_array([$controller, $params[1]], self::$_matches);
		}
		return call_user_func_array($controller, self::$_matches);
	}

	//pour les méthodes get
	public static function run()
	{
		if (empty(self::$_routes)) {
			return false;
		}
		foreach (self::$_routes as $route => $controller) {
			if (self::matchRessource(self::$_ressourceRoute, $route) || self::match($_GET['url'], $route)) {
				return self::called($controller);
			}
		}
		return self::called('base@notFound');
	}

	public static function get($route, $controllerCall)
	{
		$route = trim($route, '/');
		self::$_routes[$route] 	= $controllerCall;
		return new self;
	}

	public static function match($url, $route)
	{
		$path = preg_replace_callback('#{[a-z_]+}#i', 'self::paramMatch', $route);
		$regex = "#^$path$#i";
		if(!preg_match($regex, $url, $matches)){
		    return false;
		}
		array_shift($matches);
		self::$_matches = $matches;
		return true;
	}

	public static function paramMatch($match)
	{
		$matcher = str_replace(['{','}'],'',$match[0]);
		if (isset(self::$_params[$matcher])) {
			return '('.self::$_params[$matcher].')';
		}
		return '([^/]+)\/?';
	}

	public function with($param, $regex)
	{
		self::$_params[$param] = str_replace(['{','}'],'',$regex);
		self::$_params[$param] = str_replace('(', '(?:', $regex);
		return new self;
	}
}