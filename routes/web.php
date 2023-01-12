<?php

use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\ProfileController;
use App\Models\Articulo;
use App\Models\Factura;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    session()->forget('carrito');
    return redirect()->route('portal');
})->name('carrito.vaciar');

Route::get('/comprar', function() {
    return view('comprar', [
        'carrito' => carrito(),
    ]);
})->middleware('auth')->name('comprar');

Route::post('/realizar_compra', function() {
    $carrito = carrito();
    DB::beginTransaction();
    $factura = new Factura();
    $factura->user_id = Auth::id();
    $factura->save();
    $lineas = $carrito->getLineas();

    $inserts = [];

    foreach ($lineas as $articulo_id => $linea) {
        $inserts[] = [
            'factura_id' => $factura->id,
            'articulo_id' => $articulo_id,
            'cantidad' => $linea->getCantidad(),
        ];
    }

    DB::table('articulo_factura')->insert($inserts);
    DB::commit();
    session()->flash('exito', 'La factura se ha generado correctamente.');
    session()->forget('carrito');
    return redirect()->route('portal');
})->middleware('auth')->name('realizar_compra');

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

Route::resource('articulos', ArticuloController::class);

require __DIR__.'/auth.php';
