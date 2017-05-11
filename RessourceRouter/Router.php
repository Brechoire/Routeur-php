<?php
/**
 * Class qui sert a gérer les urls
 */
class Router
{
	/**
	 * controller a appeler
	 * @var string
	 */
	private static $_controller;

	/**
	 * toutes les routes stocké
	 * @var array
	 */
	private static $_routes 			= [];

	/**
	 * les correspondance
	 * @var array
	 */
	private static $_matches 			= [];

	/**
	 * les correspondance des ressources
	 * @var array
	 */
	private static $_ressourcesMatches 	= [];

	/**
	 * le tableau des paramètres
	 * @var array
	 */
	private static $_paramMatch 		= [];

	/**
	 * tableau des paramètres
	 * @var array
	 */
	private static $_params     		= [];

	/**
	 * la route des ressource a stocké
	 * @var string
	 */
	private static $_ressourceRoute;
	
/**
 * methode pour les ressources
 * @param  string $route   controlleur commun
 * @param  array  $actions ensemble des routes
 * @return void          retourne rien
 */
	public static function ressources($route, $actions =[])
	{
		$route = trim($route, '/');
		$url = $_GET['url'];
		$path = preg_replace('#\*#', '([a-z0-9]*)/?', $route);
		$routes = explode('/', $url);
		preg_match('#[a-z]+#i', $path, $controller);
		self::$_ressourceRoute = !empty($routes[0]) ? $routes[0] : 'index';
		self::$_matches = array_slice($routes, 1);
		foreach ($actions as $action) {
			self::$_routes[$action] = $controller[0].'@'.$action;
		}
	}

/**
 * trouver la ressources correspondant dans l'url
 * @param  string $regex  expression chercher
 * @param  string $acCtrl controlleur
 * @return bool         retourne true|false
 */
	public static function matchRessource($regex, $acCtrl)
	{
		if(!preg_match("#$regex#", $acCtrl)){
			return false;
		}else{
			return true;	
		}
	}

/**
 * appel le controlleur et la méthode
 * @param  string $controller controlleur a appeler
 * @return string|method             retourne la méthode
 */
	public static function called($controller)
	{
		if (is_string($controller)) {
			require_once 'controllers/BaseController.php';
			$params = explode('@', $controller);
	        $controller = $params[0] . "Controller";
	        if (!file_exists("controllers/".$controller.'.php')) { //si fichier existe pas
	        	return self::called('base@notFound');
	        }
	        require_once "controllers/".$controller.'.php';
	        $controller = new $controller();
	        if (!method_exists($controller, $params[1])) { //si la méthode existe pas
	        	return self::called('base@notFound');
	        }
	        if (empty(self::$_matches)) {
	        	self::$_matches = [null]; //si params existe pas, il passe un tableau null
	        }
	        return call_user_func_array([$controller, $params[1]], self::$_matches);
		}
		return call_user_func_array($controller, self::$_matches);
	}

/**
 * mise en marche du routeur
 * @return string|method retourne (l'objet a appeler)
 */
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

/**
 * créer une route simple
 * @param  string $route          route a inclure dans le tableau des routes
 * @param  string $controllerCall controller a appeller
 * @return object                 retourne l'objet en cours
 */
	public static function get($route, $controllerCall)
	{
		$route = trim($route, '/');
		self::$_routes[$route] 	= $controllerCall;
		return new self;
	}

/**
 * trouve la correspondance entre les routes et l'url
 * @param  string $url   url a comparé
 * @param  string $route route avec laquelle le comparé
 * @return bool        retourne si trouvé
 */
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

/**
 * récupère et stocke les paramètres
 * @param  string $match correspondance
 * @return string        retourne le pattern
 */
	public static function paramMatch($match)
	{
		$matcher = str_replace(['{','}'],'',$match[0]);
		if (isset(self::$_params[$matcher])) {
			return '('.self::$_params[$matcher].')';
		}
		return '([^/]+)\/?';
	}

/**
 * vérifier la validité des paramètres
 * @param  string $param nom du parametre a verifier
 * @param  string $regex regex a vérifier
 * @return object        retiourne l'objet en cours
 */
	public function with($param, $regex)
	{
		self::$_params[$param] = str_replace(['{','}'],'',$regex);
		self::$_params[$param] = str_replace('(', '(?:', $regex);
		return new self;
	}
}
