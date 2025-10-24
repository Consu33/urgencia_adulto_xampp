<?php

namespace App\Helpers;

class RutHelper
{
    public static function normalizar($rut)
    {
        // Elimina formatos comunes y asegura el formato estándar
        $rut = preg_replace('/[^0-9kK]/', '', $rut); // elimina puntos, guiones, espacios
        $dv = strtoupper(substr($rut, -1));
        $numero = substr($rut, 0, -1);

        return $numero . '-' . $dv;
    }
}