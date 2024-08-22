<?php
$router->get('/reportes', 'ReporteController@index');
$router->post('/reportes/generarPDF', 'ReporteController@generarPDF');
$router->get('/panel_control', 'PanelControlController@index');
?>