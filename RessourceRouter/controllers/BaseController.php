<?php 
/**
* 
*/
class BaseController
{
	public function notFound()
	{
		echo "<h1>Page introuvable</h1>";
	}

	public function with($regex, $name)
	{
		if (preg_match("#$regex#iu", $name, $matches)) {
			return true;
		}
		return false;
	}
}
