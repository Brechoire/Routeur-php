<?php 
class userController extends BaseController
{
	
	public function index()
	{
		echo "methode index userController ";
	}

	public function show()
	{
		echo "methode show userController";
	}

	public function all()
	{
		echo "methode all userController";
	}

	public function test($id)
	{
		echo "methode test $id userController";
	
	}
	public function home()
	{
		echo "methode home dans userController";
	}
}
