<?php

use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\ProfileController;
use App\Models\Articulo;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('portal', [
        'articulos' => Articulo::all(),
        'carrito' => carrito(),
    ]);
})->name('portal');

Route::get('/carrito/insertar/{id}', function ($id) {
    $articulo = Articulo::findOrFail($id);
    if ($articulo->stock <= 0) {
        session()->flash('error', 'No hay existencias suficientes.');
    } else {
        $carrito = carrito();
        $carrito->insertar($id);
        session()->put('carrito', $carrito);
    }
    return redirect()->route('portal');
})->name('carrito.insertar')->whereNumber('id');

Route::get('/carrito/vaciar', function () {
    session()->remove('carrito');
    return redirect()->route('portal');
})->name('carrito.vaciar');


Route::get('/prueba/{nombre?}/{apellidos?}', function ($nombre = null, $apellidos = null) {
    if ($nombre == null) {
        // return response()->redirectTo('/');
        return Response::redirectTo('/');
    }
    return view('prueba', [
        'nombre' => $nombre,
        'apellidos' => $apellidos,
    ]);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::resource('articulo', ArticuloController::class);

require __DIR__.'/auth.php';
