<?php

defined('BASEPATH') or exit('No direct script access allowed');

$route['institutions/institution/(:num)/(:any)'] = 'institution/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['institutions/list'] = 'myinstitution/list';
$route['institutions/show/(:num)/(:any)'] = 'myinstitution/show/$1/$2';
$route['institutions/office/(:num)/(:any)'] = 'myinstitution/office/$1/$2';
$route['institutions/pdf/(:num)'] = 'myinstitution/pdf/$1';
$route['institutions/office_pdf/(:num)'] = 'myinstitution/office_pdf/$1';
