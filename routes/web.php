<?php

$novaPath = config('nova.path');

Route::middleware('auth.sso')->get('/auth'.$novaPath, function() use ($novaPath) {
    Log::debug("Redir to nova");
    return redirect('..'.$novaPath);
});

Route::as('nova.login')->get($novaPath.'/login', function() use ($novaPath) {
    Log::debug("Redir to sso auth");
    return redirect('/sso/auth'.$novaPath);
});

Route::get('/auth/logout', ['as' => 'nova.logout', 'uses' => 'LoginController@logout']);
