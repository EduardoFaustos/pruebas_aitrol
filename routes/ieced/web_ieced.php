<?php

Route::get('ieced/dashboard2', 'ieced\IecedController@index2')->name('ieced.dashboard2');
Route::get('ieced/modulo', 'ieced\IecedController@modulo')->name('ieced.modulo');
Route::get('ieced/modulo2', 'ieced\IecedController@modulo2')->name('ieced.modulo2');
Route::get('ieced/submodulos/{id}', 'ieced\IecedController@submodulos')->name('ieced.submodulos');
Route::get('ieced/opciones/{id}', 'ieced\IecedController@opciones')->name('ieced.opciones');
