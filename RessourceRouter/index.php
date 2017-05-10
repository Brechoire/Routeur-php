<?php
/*
Test routeur
 */
require 'Router.php';
echo "<pre>";
Router::get('/','user@index');
Router::get('/user-{id}/', 'user@test');
Router::get('/user/', 'user@show');
Router::get('/all/', 'user@all');
Router::ressources('/post/*', ['index','show','view', 'services']);
Router::get('/home/', 'user@home');
Router::run();
?>