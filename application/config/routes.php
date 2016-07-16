<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
//$route['default_controller'] = 'welcome';
//$route['404_override'] = '';
//$route['translate_uri_dashes'] = FALSE;

//view_by_id
//$route['news/(:any)'] = 'news/view_by_id/$1';

//index
$route['news'] = 'news';
$route['news/save'] = 'news/save';


//list_in
// the regex pattern matches any alpha-numeric character set 
// followed by a "," and another alpha-numeric character set (optional) 
// 
// Examples /news/search/1456502578  
//           news/search/1456502578, 
//           news/search/1456502578,abcdefg
//           news/search/1456502578,1453507200,

$route['news/search/(((\w+)[,_-])+(\w+)?)'] = 'news/list_in/$1';



//list_where
$route['news/search/(:any)'] = 'news/list_where/$1';
$route['news/list_where/'] = 'news/list_where/';


//list_between
$route['news/search/(:num)/(:num)'] = 'news/list_between/$1/$2';

//list_where
$route['news/(:num)'] = 'news/view_by_id/$1';
//$route['(:any)'] = 'pages';
//$route['default_controller'] = 'pages/view';