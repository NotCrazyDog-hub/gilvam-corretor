<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
}

public function mapa(Imovel $imovel)
{
    return view('mapInterface', compact('imovel'));
}
