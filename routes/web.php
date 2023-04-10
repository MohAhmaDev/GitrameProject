<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
    // $user = \App\Models\User::whereDoesntHave('roles', function($query) {
    //     $query->where('name', 'admin');
    // })->get();
    // // $role = $user->roles;
    // dd($user);
    // dd($user->roles()->where('name', 'admin')->get()->name);
});


// Route::middleware(['role:admin'])->group(function () {

//     Route::get('/private', function () {
//         return "Bonjour admin";
//     });
// });