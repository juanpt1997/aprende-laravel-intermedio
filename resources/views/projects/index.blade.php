@extends('layout')

@section('title', 'Projects')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @isset($category)
                <div>
                    <h1 class="display-4 mb-0">{{ $category->name }}</h1>
                    <a href="{{ route('projects.index') }}">Regresar al portafolio</a>
                </div>
            @else
                <h1 class="display-4 mb-0">@lang('Projects')</h1>
            @endisset
            {{-- @can('create', new App\Project()) --}}
            @can('create', $newProject)
                {{-- I returned this from the controller: 'newProject' => new Project, --}}
                <a class="btn btn-primary" href="{{ route('projects.create') }}">Crear proyecto</a>
            @endcan
        </div>
        <p class="lead text-secondary">Proyectos realizados Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>

        <div class="d-flex flex-wrap justify-content-between align-items-start">
            @forelse($projects as $project)
                <div class="card border-0 shadow-sm mt-4 mx-auto" style="width: 18rem;">
                    @if ($project->image)
                        <img class="card-img-top" style="height:150px; object-fit:cover"
                            src="{{ asset('/storage/' . $project->image) }}" alt="">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('projects.show', $project) }}">
                                {{ $project->title }}
                            </a>
                        </h5>
                        <h6 class="card-subtitle">{{ $project->created_at->format('d/m/Y') }}</h6>
                        <p class="card-text text-truncate">{{ $project->description }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-primary btn-sm">Ver más...</a>
                            @if ($project->category_id)
                                <a href="{{ route('categories.show', $project->category) }}"
                                    class="badge badge-secondary">{{ $project->category->name }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body">
                        No hay proyectos para mostrar
                    </div>
                </div>
            @endforelse
        </div>
        <div class="mt-4">
            {{ $projects->links() }}
        </div>

        @can('view-deleted-projects')
            <h4>Papelera</h4>
            <ul class="list-group">
                @foreach ($deletedProjects as $deletedProject)
                    <li class="list-group-item">
                        {{ $deletedProject->title }}
                        @can('restore', $deletedProject)
                            <form method="POST" action="{{ route('projects.restore', $deletedProject) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-info">Restaurar</button>
                            </form>
                        @endcan
                        {{-- @can('forceDelete', $deletedProject) --}} {{-- ? Camel case and kebab case are both accepted event if the method's name is forceDelete --}}
                        @can('force-delete', $deletedProject)
                            <form 
                                onsubmit="return confirm('Esta acción no se puede deshacer, ¿Estás seguro de querer eliminar este proyecto?')"
                                action="{{ route('projects.forceDelete', $deletedProject) }}" method="post" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Eliminar permanentemente</button>
                            </form>
                        @endcan
                    </li>
                @endforeach
            </ul>
        @endcan
    </div>
@endsection
