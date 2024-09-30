<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Note extends Model
{
    use HasFactory;

    // Campos que pueden ser asignados en masa
    protected $fillable = [
        'title',
        'description',
        'user_id',
        'due_date',
        'image',
    ];

    // Las fechas que serán tratadas como instancias de Carbon
    protected $dates = ['due_date', 'created_at', 'updated_at'];

    // Relación con el modelo User (asumiendo que una nota pertenece a un usuario)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con las etiquetas (si usas una tabla pivot para tags)
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // Un método accesor para verificar si la nota está vencida
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast();
    }

    // Scope para ordenar notas por fecha de vencimiento (si lo necesitas en las consultas)
    public function scopeOrderByDueDate($query)
    {
        return $query->orderBy('due_date', 'asc');
    }
}
