<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function show(Request $request) {
        $user = $request->user();
        $user->load('drivers');

        return $request;
    }

    public function uopdate(Request $request) {
        $request->validate([
            'year' => ['required', 'numeric', 'between:2010,2025'],
            'maker' => ['required'],
            'model' => ['required'],
            'color' => ['required'],
            'license_plate' => ['required'],
            'name' => ['required'],
        ]);

        $user = $request->user();

        $user->update($request->only('name'));

        //create or update driver associated with this user
        $user->drivers()->updateOrCreate($request->only([
            'year',
            'maker',
            'model',
            'color',
            'license_plate'
        ]));

        $user->load('drivers');

        return $user;
    }
}
