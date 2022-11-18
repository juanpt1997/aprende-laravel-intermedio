<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'url';
    }

    // Podemos pre cargar una consulta así:
    // $project = Project::with('category')->find(2);
    // Esto me trae el project incluida la category para evitar tantas consultas
    // La próxima vez con $project->category no realizará una consulta a la db
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
