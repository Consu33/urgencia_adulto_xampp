<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Atencion;

class Paciente extends Model
{

    protected $fillable = [
    'nombre',
    'apellido',
    'rut',
    'categoria_id',
    'estado_id'
    ];

    // Esto asegura que el atributo 'activo' sea tratado como un booleano
    protected $casts = [
        'activo' => 'boolean',
    ];


    public function categoria() {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function estado() {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function atenciones()
    {
        return $this->hasMany(Atencion::class);
    }

}
