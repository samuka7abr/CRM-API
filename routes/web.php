<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'CRM API funcionando!']);
});

Route::get('/teste', function () {
    return response()->json(['message' => 'Teste funcionando!']);
});
