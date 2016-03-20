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
$route['ubicaciones/imagenes/(:any)'] = "imagenes_ubicaciones/index/index/$1";
$route['ubicaciones/imagenes'] = "imagenes_ubicaciones/index/index";
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
$route['briefs/(:any)'] = "briefs/index/index/$1";
$route['briefs'] = "briefs/index/index";
$route['archivos/(:any)'] = "archivos/index/index/$1";
$route['archivos'] = "archivos/index/index";
$route['clientes/(:any)/contactos/(:any)'] = "clientes/contactos/relation/$1/$2";
$route['clientes/(:any)/contactos'] = "clientes/contactos/relation/$1";
$route['clientes/contactos/(:any)'] = "clientes/contactos/index/$1";
$route['clientes/(:any)/acciones/(:any)'] = "clientes/acciones/relation/$1/$2";
$route['clientes/(:any)/acciones'] = "clientes/acciones/relation/$1";
$route['clientes/contactos'] = "clientes/contactos/index";
$route['clientes/(:any)'] = "clientes/index/index/$1";
$route['clientes'] = "clientes/index/index";
$route['agencias/(:any)/comisiones/(:any)'] = "agencias/comisiones/relation/$1/$2";
$route['agencias/(:any)/comisiones'] = "agencias/comisiones/relation/$1";
$route['agencias/comisiones/(:any)'] = "agencias/comisiones/index/$1";
$route['agencias/comisiones'] = "agencias/comisiones/index";
$route['agencias/(:any)'] = "agencias/index/index/$1";
$route['agencias'] = "agencias/index/index";
$route['acciones_clientes/tipos/(:any)'] = "acciones_clientes/tipos/index/$1";
$route['acciones_clientes/tipos'] = "acciones_clientes/tipos/index";
$route['acciones_clientes/(:any)'] = "acciones_clientes/index/index/$1";
$route['acciones_clientes'] = "acciones_clientes/index/index";
$route['clientes/acciones/(:any)'] = "acciones_clientes/index/index/$1";
$route['clientes/acciones'] = "acciones_clientes/index/index";
$route['paises/(:any)'] = "paises/index/index/$1";
$route['paises'] = "paises/index/index";
$route['plazas/(:any)'] = "plazas/index/index/$1";
$route['plazas'] = "plazas/index/index";
$route['medios/tipos/(:any)'] = "tipos_medios/index/index/$1";
$route['medios/tipos'] = "tipos_medios/index/index";
$route['medios/subtipos/(:any)'] = "subtipos_medios/index/index/$1";
$route['medios/subtipos'] = "subtipos_medios/index/index";
$route['propuestas/categorias/(:any)'] = "categorias_propuestas/index/index/$1";
$route['propuestas/categorias'] = "categorias_propuestas/index/index";
$route['ordenes/imagenes/(:any)'] = "imagenes_ordenes/index/index/$1";
$route['ordenes/imagenes'] = "imagenes_ordenes/index/index";
$route['ordenes/(:any)'] = "ordenes_trabajo/index/index/$1";
$route['ordenes'] = "ordenes_trabajo/index/index";
$route['incidencias/imagenes/(:any)'] = "imagenes_incidencias/index/index/$1";
$route['incidencias/imagenes'] = "imagenes_incidencias/index/index";
$route['incidencias/(:any)'] = "incidencias/index/index/$1";
$route['incidencias'] = "incidencias/index/index";


$route['default_controller'] = "doc/doc";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */