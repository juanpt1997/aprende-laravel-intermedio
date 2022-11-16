<?php

namespace App\Listeners;

use App\Events\ProjectSaved;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OptimizeProjectImage implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ProjectSaved  $event
     * @return void
     */
    public function handle(ProjectSaved $event)
    {
        // ? De esta forma estamos atados al disco local
        // $img = Image::make(storage_path('app/public/' . $project->image));
        $img = Image::make(Storage::get('public/' . $event->project->image))
            ->widen(600)
            ->limitColors(255)
            ->encode();
        // Es posible recortar
        // Limitar colores...haciendo pruebas me da que pesa más
        // $img->widen(600)->limitColors(255)->encode();
        Storage::put('public/' . $event->project->image, (string) $img);
    }
}
