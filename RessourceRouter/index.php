<?php
/*
Test routeur
 */
require 'Router.php';
Router::get('/','user@index');
Router::get('/user-{id}/', 'user@test')->with('id','[1-9]');
Router::get('/users/', 'user@show');
Router::get('/all/', 'user@all');
Router::ressources('/post/*', ['index','show','view', 'services']);
Router::get('/home/', 'user@home');
Router::run(); // Ne sourtout pas oublier, sinon ça ne marchera pas
?>
