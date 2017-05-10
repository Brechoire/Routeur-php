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
	private static $_ressourceController;
	
	public static function ressources($route, $actions =[])
	{
		$route = trim($route, '/');
		$url = $_GET['url'];
		$path = preg_replace('#\*#', '([a-z0-9]*)/?', $route);
		
		$routes = explode('/', $url);
		$controller = isset($routes[0]) ? $routes[0] : null;
		$action = !empty($routes[1]) ? $routes[1] : 'index';
		self::$_ressourceRoute = $controller.'/'.$action;
		self::$_ressourceController = $controller.'@'.$action;
		self::$_matches = array_slice($routes, 2);
		foreach ($actions as $action) {
			self::$_routes[$controller.'/'.$action] = $controller.'@'.$action;
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

	/*
	Méthodes Autre router
	 */
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

	public static function called($controller)
	{
		if (is_string($controller)) {
			require_once 'controllers/BaseController.php';
			$params = explode('@', $controller);
	        $controller = $params[0] . "Controller";
	        require_once "controllers/".$controller.'.php';
	        $controller = new $controller();
	        if (!method_exists($controller, $params[1])) {
	        	return self::called('base@notFound');
	        }
	        return call_user_func_array([$controller, $params[1]], self::$_matches);
		}
		return call_user_func_array($controller, self::$_matches);
	}

	public static function run()
	{
		if (empty(self::$_routes)) {
			echo "route vide";
			return false;
		}
		var_dump(self::$_routes);
		foreach (self::$_routes as $route => $controller) {
			if (self::match($_GET['url'], $route) || !self::matchRessource(self::$_ressourceRoute, self::$_ressourceController)) {
				echo "j'ai trouvé <br>";
				return self::called($controller);
			}
		}
	}
}