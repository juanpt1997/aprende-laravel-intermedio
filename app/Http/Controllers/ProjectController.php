<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\SaveProjectRequest;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'show');
    }

    public function index()
    {
        return view('projects.index', [
            'projects' => Project::latest()->paginate()
        ]);
    }

    public function show(Project $project)
    {
        return view('projects.show', [
            'project' => $project
        ]);
    }

    public function create()
    {
        return view('projects.create', [
            'project' => new Project
        ]);
    }

    public function store(SaveProjectRequest $request)
    {
        // return $request->file('image')->store('images'); // ? Por defecto se puede utilizar así pero pasa el de local en config/filesystems.php
        // return $request->file('image')->store('images', 'local'); // ? Esto equivale a lo de arriba, podemos cambiar el disco por defecto desde .env
        // return $request->file('image')->store('images', 'public');
        // Project::create( $request->validated() ); // ? No lo usaremos así porque lo vamos a optimizar así:

        $project = new Project($request->validated());
        $project->image = $request->file('image')->store('images', 'public');
        $project->save();

        // Explicación en el método update
        $img = Image::make(Storage::get('public/' . $project->image))
            ->widen(600)
            ->limitColors(255)
            ->encode();
        Storage::put('public/' . $project->image, (string) $img);

        return redirect()->route('projects.index')->with('status', 'El proyecto fue creado con éxito');
    }

    public function edit(Project $project)
    {
        return view('projects.edit', [
            'project' => $project
        ]);
    }

    public function update(Project $project, SaveProjectRequest $request)
    {
        // ? Con array_filter eliminamos el campo imagen del arreglo en caso de venir nulo
        //dd(array_filter($request->validated()));

        if ($request->hasFile('image')) {
            // Elimino la imagen anterior, debo pasarle la ruta, recordar que el disco no es local si no public
            Storage::delete('public/' . $project->image);
            // Guardo
            $project = $project->fill($request->validated());
            $project->image = $request->file('image')->store('images', 'public');
            $project->save();

            // Optimizar la imagen que se ha guardado
            // ? De esta forma estamos atados al disco local
            // $img = Image::make(storage_path('app/public/' . $project->image));
            $img = Image::make(Storage::get('public/' . $project->image))
                ->widen(600)
                ->limitColors(255)
                ->encode();
            // Es posible recortar
            // Limitar colores...haciendo pruebas me da que pesa más
            // $img->widen(600)->limitColors(255)->encode();
            Storage::put('public/' . $project->image, (string) $img);
        } else {
            $project->update(array_filter($request->validated()));
        }

        return redirect()->route('projects.show', $project)->with('status', 'El proyecto fue actualizado con éxito.');
    }

    public function destroy(Project $project)
    {
        Storage::delete('public/' . $project->image);

        $project->delete();

        return redirect()->route('projects.index')->with('status', 'El proyecto fue eliminado con éxito.');
    }
}
