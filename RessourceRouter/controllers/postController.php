<?php 
class postController extends BaseController
{
	
	public function show()
	{
		echo "methode show postController ";
	}

	public function index()
	{
		echo "methode index postController";
	}
	public function view($id)
	{
		echo "methode view : $id postController";
	}

	public function services()
	{
		echo "methode services postController";
	}
}