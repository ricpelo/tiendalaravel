<?php

function carrito()
{
    if (session()->missing('carrito')) {
        session()->put('carrito', new \App\Generico\Carrito());
    }

    return session('carrito');
}

function dinero($s)
{
    return number_format($s, 2, ',', ' ') . ' €';
}
