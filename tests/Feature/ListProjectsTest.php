<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListProjectsTest extends TestCase
{
    // php artisan migrate
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanSeeAllProjects()
    {
        $this->withExceptionHandling(); // See complete errors



        // Setup
        // Creo un proyecto
        $project = factory(Project::class)->create();
        // otro proyecto
        $project2 = factory(Project::class)->create();

        $response = $this->get(route('projects.index'));

        $response->assertStatus(200);

        // Check if returns the view we wait
        $response->assertViewIs('projects.index');

        // Check if a variable exists in the view
        $response->assertViewHas('projects');

        // We check if the project title exists
        $response->assertSee($project->title);
        $response->assertSee($project2->title);
    }

    public function testCanSeeIndividualProjects(){
        $this->withExceptionHandling(); // See complete errors

        // Setup
        // Creo un proyecto
        $project = factory(Project::class)->create();
        // otro proyecto (no se debería visualizar)
        $project2 = factory(Project::class)->create();

        $response = $this->get(route('projects.show', $project));
        $response->assertSee($project->title);
        $response->assertDontSee($project2->title);
    }
}
