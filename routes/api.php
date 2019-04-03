<?php

Route::middleware('auth.sso')->get('/auth/sso-token', function() {
    if(auth()->user() != null)
        return response()->json([
            'token' => auth()->user()->api_token
        ]);

    return abort(403);
});

Route::middleware('auth:api')->get('/auth/token', function() {
    if(auth()->user() != null)
        return response()->json([
            'token' => auth()->user()->api_token
        ]);

    return abort(403);
});
