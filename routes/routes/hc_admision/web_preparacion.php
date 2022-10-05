<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
 
//consulta máster




Route::get('historiaclinica/preparacion/{ag}/{url}', 'hc_admision\PreparacionController@mostrar')->name('preparacion.mostrar');

Route::get('preparaciones/pdf/endoscopia', 'hc_admision\PreparacionController@pdfPreparaciones')->name('pdfPreparaciones');
Route::get('preparaciones/pdf/colonoscopia_travadpikOral', 'hc_admision\PreparacionController@pdfPreparacionesColonoscopiaTO') ->name('pdfPreparacionesColonoscopiaTO');
Route::get('preparaciones/pdf/colonoscopia_travadpikOral12', 'hc_admision\PreparacionController@pdfPreparacionesColonoscopiaTO12') ->name('pdfPreparacionesColonoscopiaTO12');
Route::get('preparaciones/pdf/colonoscopia_nulytely', 'hc_admision\PreparacionController@pdfPreparacionesColonoscopiaN') ->name('pdfPreparacionesColonoscopiaN');
Route::get('preparaciones/pdf/colonoscopia_izinova', 'hc_admision\PreparacionController@pdfPreparacionesColonoscopiaI') ->name('pdfPreparacionesColonoscopiaI');
Route::get('preparaciones/pdf/colonoscopia_izinova12', 'hc_admision\PreparacionController@pdfPreparacionesColonoscopiaI12') ->name('pdfPreparacionesColonoscopiaI12');
Route::get('preparaciones/pdf/capsula_endoscopica', 'hc_admision\PreparacionController@pdfPreparacionesCapsulaE') ->name('pdfPreparacionesCapsulaE');
Route::get('preparaciones/pdf/broncoscopia', 'hc_admision\PreparacionController@pdfPreparacionesBroncoscopia') ->name('pdfPreparacionesBroncoscopia');
Route::get('preparaciones/pdf/colonoscopia_neolaxOral', 'hc_admision\PreparacionController@pdfPreparacionesColonoscopia_NO') ->name('pdfPreparacionesColonoscopia_NO');
Route::get('preparaciones/pdf/endoscopia', 'hc_admision\PreparacionController@pdfPreparacionesEndoscopia') ->name('pdfPreparacionesEndoscopia');
Route::get('preparaciones/pdf/ecoendoscopia_diagnostica_puncion', 'hc_admision\PreparacionController@pdfPreparacionesEcoendoscopiaDCP') ->name('pdfPreparacionesEcoendoscopiaDCP');
Route::get('preparaciones/pdf/retiro_balon_intragastrico', 'hc_admision\PreparacionController@pdfPreparacionesRetiroBI') ->name('pdfPreparacionesRetiroBI');
Route::get('preparaciones/pdf/POEM', 'hc_admision\PreparacionController@pdfPreparacionesPOEM') ->name('pdfPreparacionesPOEM');
Route::get('preparaciones/pdf/manometria_rectal', 'hc_admision\PreparacionController@pdfPreparacionesManometriaAR') ->name('pdfPreparacionesManometriaAR');
Route::get('preparaciones/pdf/manometria_esofagica', 'hc_admision\PreparacionController@pdfPreparacionesManometriaE') ->name('pdfPreparacionesManometriaE');
