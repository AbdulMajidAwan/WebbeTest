<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\Menu;
use App\Models\MenuItem;

class ExampleTest extends TestCase
{
    public function testWarmupEvents() {
        $datePast = (new Carbon())->subYear()->setDay(21);
        $dateFuture = (new Carbon())->addYears(1);

        $response = $this->get('/warmupevents');
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonPath('0.name', 'Laravel convention '.$datePast->year)
            ->assertJsonPath('1.name', 'Laravel convention '.$dateFuture->year)
            ->assertJsonPath('2.name', 'React convention '.$dateFuture->year);
    }

    public function testEvents() {
        $datePast = (new Carbon())->subYear()->setDay(21);
        $dateFuture = (new Carbon())->addYears(1);

        $response = $this->get('/events');
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonPath('0.name', 'Laravel convention '.$datePast->year)
            ->assertJsonPath('0.workshops.0.name', 'Illuminate your knowledge of the laravel code base')
            ->assertJsonPath('1.name', 'Laravel convention '.$dateFuture->year)
            ->assertJsonPath('1.workshops.0.name', 'The new Eloquent - load more with less')
            ->assertJsonPath('1.workshops.1.name', 'AutoEx - handles exceptions 100% automatic')
            ->assertJsonPath('2.name', 'React convention '.$dateFuture->year)
            ->assertJsonPath('2.workshops.0.name', '#NoClass pure functional programming')
            ->assertJsonPath('2.workshops.1.name', 'Navigating the function jungle');
    }

    public function testFutureEvents() {
        $dateFuture = (new Carbon())->addYears(1);

        $response = $this->get('/futureevents');
        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonPath('0.name', 'Laravel convention '.$dateFuture->year)
            ->assertJsonPath('0.workshops.0.name', 'The new Eloquent - load more with less')
            ->assertJsonPath('0.workshops.1.name', 'AutoEx - handles exceptions 100% automatic')
            ->assertJsonPath('1.name', 'React convention '.$dateFuture->year)
            ->assertJsonPath('1.workshops.0.name', '#NoClass pure functional programming')
            ->assertJsonPath('1.workshops.1.name', 'Navigating the function jungle');
    }

    public function testMenu()
    {
        // Create menu items with nested structure
        $topLevelMenuItem = MenuItem::factory()->create(['parent_id' => null, 'order' => 1, 'name' => 'Laracon']);
        $childMenuItem1 = MenuItem::factory()->create(['parent_id' => $topLevelMenuItem->id, 'order' => 1, 'name' => 'Illuminate', 'url' => '/events/laracon/workshops/illuminate']);
        $childMenuItem2 = MenuItem::factory()->create(['parent_id' => $topLevelMenuItem->id, 'order' => 2, 'name' => 'Eloquent', 'url' => '/events/laracon/workshops/eloquent']);

        // Call the getMenuItems function
        $response = $this->get('/menu');

        // Assert the response status
        $response->assertStatus(200);

        // Assert the response structure and content
        $response->assertJsonCount(1); // Assert there is one top-level menu item

        $response->assertJson([
            [
                'name' => 'Laracon',
                'children' => [
                    [
                        'name' => 'Illuminate',
                        'url' => '/events/laracon/workshops/illuminate',
                    ],
                    [
                        'name' => 'Eloquent',
                        'url' => '/events/laracon/workshops/eloquent',
                    ],
                ],
            ],
        ]);
    }

}
