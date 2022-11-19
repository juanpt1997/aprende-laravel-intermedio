<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    public function getRouteKeyName()
    {
        return 'url';
    }

     // Podemos pre cargar una consulta así:
    // $category = Category::with('projects')->first();
    // Esto me trae el project incluida los projects para evitar tantas consultas
    // La próxima vez con $category->projects no realizará una consulta a la db
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
