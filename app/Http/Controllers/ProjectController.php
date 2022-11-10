<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;
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
        if ($request->file('image')) $project->image = $request->file('image')->store('images', 'public');
        $project->save();

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
        $project->update( $request->validated() );

        return redirect()->route('projects.show', $project)->with('status', 'El proyecto fue actualizado con éxito.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('status', 'El proyecto fue eliminado con éxito.');
    }
}
