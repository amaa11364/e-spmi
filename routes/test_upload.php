<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('/test-upload', function(Request $request) {
    return response()->json([
        'has_files' => $request->hasFile('files'),
        'all' => array_keys($request->all()),
        'files' => array_keys($request->file()),
    ]);
});
