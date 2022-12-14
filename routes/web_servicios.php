<?php
//SERVICIOS PALPATINE
Route::match(['get','post'],'api/login', 'servicios\ServiciosController@login');
Route::match(['get','post'],'api/loginApp', 'servicios\ServiciosIecedController@login');
Route::post('api/getuserinfo', 'servicios\ServiciosController@getuserinfo');
Route::post('api/registeruser', 'servicios\ServiciosController@registerUser');
Route::post('api/seguros', 'servicios\ServiciosController@getSeguros');
Route::post('api/myExam', 'servicios\ServiciosController@myExam');
Route::post('api/getBanners', 'servicios\ServiciosController@getBanners');
Route::post('api/createOrderGetPayment', 'servicios\ServiciosController@createOrderGetPayment');
Route::post('api/iecedcreateOrderGetPayment', 'servicios\ServiciosIecedController@createOrderGetPayment');
Route::post('api/getPaymentInformation', 'servicios\ServiciosController@getPaymentInformation');
Route::post('api/iecedgetPaymentInformation', 'servicios\ServiciosIecedController@getPaymentInformation');
Route::get('api/returnUrl', 'servicios\ServiciosController@returnUrl');
Route::get('api/cancelUrl', 'servicios\ServiciosController@cancelUrl');
Route::get('api/postprocessUrl', 'servicios\ServiciosController@postprocessUrl');
Route::get('api/loadPDF', 'servicios\ServiciosController@pdf')->name('api.Loadhtml');
Route::post('api/getExam', 'servicios\ServiciosController@getExam');
Route::get('api/previsualizedExam/{id}', 'servicios\ServiciosController@getPDF')->name('api.Previsualized');
Route::get('api/visualizerExam', 'servicios\ServiciosController@returnHtml')->name('api.visualizer');
Route::get('hospital/login', 'servicios\ServiciosController@logins')->name('hospital.login');
Route::get('api/data', 'servicios\ServiciosController@loadData')->name('api.loadData');
Route::get('api/daily', 'servicios\ServiciosIecedController@shedule')->name('api.daily');
Route::get('api/reedireccionar', 'servicios\ServiciosController@loadUserAgent')->name('api.loadUserAgent');
Route::get('api/GetInvoice', 'servicios\ServiciosIecedController@get_pay_app')->name('api.get_pay_app');
Route::match(['get','post'],'iecedapi/login', 'servicios\ServiciosIecedController@login');
Route::post('api/listDr', 'servicios\ServiciosIecedController@drlist');
Route::get('api/GenerarExamen', 'servicios\ServiciosIecedController@generarOrdenExamen');
Route::post('api/iecedBanners', 'servicios\ServiciosIecedController@banners');
Route::post('api/iecedCharlas', 'servicios\ServiciosIecedController@charlas');
Route::post('api/iecedMembresias', 'servicios\ServiciosIecedController@membresias');
Route::post('api/iecedHistorial', 'servicios\ServiciosIecedController@historialConsultas');
Route::post('api/misRecetas', 'servicios\ServiciosIecedController@historialRecetas');
Route::get('api/recetaPDFvisualiza', 'servicios\ServiciosIecedController@pdf')->name('api.VisualizedReceta');
Route::get('api/recetaPdf/{id}/{tipo}', 'servicios\ServiciosIecedController@pdfReceta')->name('api.Receta');
Route::post('api/notificaciones/charlas', 'servicios\ServiciosIecedController@notificacionCharlas')->name('api.notificacionCharlas');
Route::post('api/validAgenda', 'servicios\ServiciosIecedController@validAgenda')->name('api.ValidAgenda');
Route::post('api/appSolicitudes', 'servicios\ServiciosIecedController@storeSolicitudes')->name('api.storeSolicitudes');
Route::post('api/procedures', 'servicios\ServiciosIecedController@procedures')->name('api.procedures');
Route::post('api/sendModalidad', 'servicios\ServiciosIecedController@store_agenda')->name('api.sendModalidad');
Route::post('api/list_Online', 'servicios\ServiciosIecedController@list_online')->name('api.list_online');
Route::post('api/store_ratings', 'servicios\ServiciosIecedController@store_ratings')->name('api.store_ratings');
Route::post('api/verify_rating', 'servicios\ServiciosIecedController@verify_rating')->name('api.verify_rating');
Route::post('api/lstore_ratings', 'servicios\ServiciosController@store_ratings')->name('api.store_ratings2');
Route::post('api/lverify_rating', 'servicios\ServiciosController@verify_rating')->name('api.verify_rating2');
Route::post('api/uploadPhoto/{id}', 'servicios\ServiciosIecedController@uploadPhoto')->name('api.uploadPhoto');
Route::post('api/misConsultas', 'servicios\ServiciosIecedController@misConsultas')->name('api.misConsultas');
Route::post('api/informacion', 'servicios\ServiciosController@informacion')->name('api.minformacion');
Route::post('api/updateprofile', 'servicios\ServiciosIecedController@updateProfile')->name('api.updateProfile');
Route::post('api/password', 'servicios\ServiciosIecedController@newPassword')->name('api.password');
Route::get('privacidad', 'servicios\ServiciosIecedController@privacidad')->name('api.privacidad');
Route::post('api/registerPlan', 'servicios\ServiciosController@addPlan')->name('api.ServiciosPlan');
Route::post('api/updateFamiliar', 'servicios\ServiciosIecedController@updateFamiliar')->name('api.updateFamiliar');
Route::post('api/loadPlan', 'servicios\ServiciosController@loadPlan')->name('api.loadPlan');
Route::post('api/removePlan', 'servicios\ServiciosController@removePlan')->name('api.removePlan');

//FARMACIA
Route::post('farma/obtener_subcategorias', 'servicios\FarmaController@obtener_subcategorias')->name('farma.obtener_subcategorias');
Route::post('farma/obtener_productos_categorias', 'servicios\FarmaController@obtener_productos_categorias')->name('farma.obtener_productos_categorias');

