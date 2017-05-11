<?php
/**
 * class de test
 */
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
		if ($id) {
			echo "methode view : $id postController";
		} else {
			echo "rin";
		}
		
	}

	public function services()
	{
		echo "methode services postController";
	}
}
