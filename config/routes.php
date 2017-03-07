<?php

/**
 * Configure manually all the routes here
 *
 * use .+ when expecting a parameter
 *
 * @author Vince Urag
 */

//USER
$route['/'] = "index";
$route['/users'] = "users";
$route['/users/:param'] = "users/index";
$route['/auth/login'] = "users/login";

//RESTO
$route['/resto'] = "restaurants";
$route['/resto/:param'] = "restaurants/index";
$route['/resto/topten'] = "restaurants/topten";
