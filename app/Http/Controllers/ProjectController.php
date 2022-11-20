<?php

namespace App\Http\Controllers;

use App\Project;
use App\Category;
use App\Events\ProjectSaved;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Gate;
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
        // ? Con ese with('category') We solve problem N + 1, for each project I won't need to get category from DB
        return view('projects.index', [
            'newProject' => new Project,
            'projects' => Project::with('category')->latest()->paginate(),
            'deletedProjects' => Project::onlyTrashed()->get()
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
        // ? Both do the same but code below is better
        // if (Gate::allows('create-projects')){
        //     $categories = Category::pluck('name', 'id');
        //     return view('projects.create', [
        //         'project' => new Project,
        //         'categories' => $categories
        //     ]);
        // }
        // abort(403);

        // abort_unless(Gate::allows('create-projects'), 403);
        // Gate::authorize('create-projects');
        // $this->authorize('create-projects');
        $this->authorize('create', $project = new Project);
        $categories = Category::pluck('name', 'id');
        return view('projects.create', [
            'project' => $project,
            'categories' => $categories
        ]);
    }

    public function store(SaveProjectRequest $request)
    {
        // return $request->file('image')->store('images'); // ? Por defecto se puede utilizar así pero pasa el de local en config/filesystems.php
        // return $request->file('image')->store('images', 'local'); // ? Esto equivale a lo de arriba, podemos cambiar el disco por defecto desde .env
        // return $request->file('image')->store('images', 'public');
        // Project::create( $request->validated() ); // ? No lo usaremos así porque lo vamos a optimizar así:

        $project = new Project($request->validated());
        // $this->authorize('create', $project);
        $project->image = $request->file('image')->store('images', 'public');
        $project->save();

        // Optimizar la imagen que se ha guardado
        // $this->optimizeImage($project); // ? esto no lo utilizaremos así debido a que creamos event y listener
        ProjectSaved::dispatch($project);

        return redirect()->route('projects.index')->with('status', 'El proyecto fue creado con éxito');
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $categories = Category::pluck('name', 'id');
        return view('projects.edit', [
            'project' => $project,
            'categories' => $categories
        ]);
    }

    public function update(Project $project, SaveProjectRequest $request)
    {
        $this->authorize('update', $project);

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
            // $this->optimizeImage($project); // ? esto no lo utilizaremos así debido a que creamos event y listener
            ProjectSaved::dispatch($project);
        } else {
            $project->update(array_filter($request->validated()));
        }

        return redirect()->route('projects.show', $project)->with('status', 'El proyecto fue actualizado con éxito.');
    }


    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        // Storage::delete('public/' . $project->image); // ? This was commented because there is the possibility that a user may want to restore a soft deleted project

        $project->delete();

        return redirect()->route('projects.index')->with('status', 'El proyecto fue eliminado con éxito.');
    }


    // ? We can't use route model binding if the projects are 'deleted'
    // public function restore(Project $project)
    public function restore($projectUrl)
    {
        $project = Project::withTrashed()->whereUrl($projectUrl)->firstOrFail();

        $this->authorize('restore', $project);

        $project->restore();

        return redirect()->route('projects.index')->with('status', 'El proyecto fue restaurado con éxito.');
    }

    // public function forceDelete(Project $project)
    public function forceDelete($projectUrl)
    {
        $project = Project::withTrashed()->whereUrl($projectUrl)->firstOrFail();

        $this->authorize('force-delete', $project); // ? Camel case and kebab case are both accepted event if the method's name is forceDelete

        Storage::delete('public/' . $project->image);

        $project->forceDelete();

        return redirect()->route('projects.index')->with('status', 'El proyecto fue eliminado permanentemente.');
    }


    // ? Optimizar la imagen que se ha guardado, esto no lo utilizaremos así debido a que creamos event y listener
    // protected function optimizeImage($project)
    // {

    // }
}
