<?php

// ? Visualizamos las consultas para solucionar posteriormente el problema de N + 1 (por cada proyecto realizo la consulta de la categoría)
// DB::listen(function($query) {
//     var_dump($query->sql);
// });
// ? También podemos utilizar el paquete laravel-debugbar

Route::view('/', 'home')->name('home');
Route::view('/quienes-somos', 'about')->name('about');

Route::resource('portafolio', 'ProjectController')
    ->parameters(['portafolio' => 'project'])
    ->names('projects');

Route::get('categorias/{category}', 'CategoryController@show')->name('categories.show');

Route::view('/contacto', 'contact')->name('contact');
Route::post('contact', 'MessageController@store')->name('messages.store');

Auth::routes(['register' => false]);
