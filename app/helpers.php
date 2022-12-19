<?php

function carrito()
{
    if (session()->missing('carrito')) {
        session()->put('carrito', new \App\Generico\Carrito());
    }

    return session('carrito');
}
