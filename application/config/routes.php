<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
| Please see the usuarios guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

//$route['usuarios/(:any)/prueba/(:any)'] = "usuarios/index/index/$1/$2";
$route['usuarios/(:any)'] = "usuarios/index/index/$1";
$route['usuarios'] = "usuarios/index/index";
$route['clientes/(:any)'] = "clientes/index/index/$1";
$route['clientes'] = "clientes/index/index";
$route['ubicaciones/(:any)'] = "ubicaciones/index/index/$1";
$route['ubicaciones'] = "ubicaciones/index/index";
$route['medios/(:any)'] = "medios/index/index/$1";
$route['medios'] = "medios/index/index";
$route['metadata/ubicaciones/foursquare/venues/(:any)'] = "metadata/foursquare_venues/index/$1";
$route['metadata/ubicaciones/foursquare/venues'] = "metadata/foursquare_venues/index";
$route['metadata/ubicaciones/foursquare/categories/(:any)'] = "metadata/foursquare_categories/index/$1";
$route['metadata/ubicaciones/foursquare/categories'] = "metadata/foursquare_categories/index";
$route['catorcenas/(:any)'] = "catorcenas/index/index/$1";
$route['catorcenas'] = "catorcenas/index/index";
$route['gastos/(:any)'] = "gastos/index/index/$1";
$route['gastos'] = "gastos/index/index";
$route['parametros/(:any)'] = "parametros/index/index/$1";
$route['parametros'] = "parametros/index/index";
$route['agencias/(:any)'] = "agencias/index/index/$1";
$route['agencias'] = "agencias/index/index";


$route['default_controller'] = "doc/doc";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */