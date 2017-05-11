<?php 
/**
* Controlleur de base: mettre toutes les méthodes dont les autres controlleurs doivent hériter
*/
class BaseController extends BaseController
{
	/**
	 * page introuvable
	 * @return void returne le message de la page introuvable
	 */
	public function notFound()
	{
		echo "<h1>Page introuvable</h1>";
		header("HTTP/1.0 404 Not Found");
	}

/**
 * verifier de nouveau si le param recu dans le controlleur correspond a un format donné
 * @param  string $regex le regex, l'expression
 * @param  string|int $name  le paramettre a verifier
 * @return bool        retourne true|false
 */
	public function with($regex, $name)
	{
		if (preg_match("#$regex#i", $name, $matches)) {
			return true;
		}
		return false;
	}
}
