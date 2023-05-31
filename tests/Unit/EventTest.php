<?php

namespace Tests\Unit;

use App\Models\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EventTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetEventsWithWorkshops()
    {
        // Create some test events with workshops
        $event1 = Event::factory()->create();
        $event1->workshops()->createMany([
            [
                'start' => '2020-02-21 10:00:00',
                'end' => '2020-02-21 16:00:00',
                'name' => 'Illuminate your knowledge of the laravel code base',
            ],
        ]);

        $event2 = Event::factory()->create();
        $event2->workshops()->createMany([
            [
                'start' => '2021-10-21 10:00:00',
                'end' => '2021-10-21 18:00:00',
                'name' => 'The new Eloquent - load more with less',
            ],
            [
                'start' => '2021-11-21 09:00:00',
                'end' => '2021-11-21 17:00:00',
                'name' => 'AutoEx - handles exceptions 100% automatic',
            ],
        ]);

        // Call the getEventsWithWorkshops function
        $events = getEventsWithWorkshops();

        // Assert that the returned events match the expected data
        $this->assertCount(2, $events);

        $this->assertEquals($event1->id, $events[0]['id']);
        $this->assertEquals($event1->name, $events[0]['name']);
        $this->assertCount(1, $events[0]['workshops']);

        $this->assertEquals($event2->id, $events[1]['id']);
        $this->assertEquals($event2->name, $events[1]['name']);
        $this->assertCount(2, $events[1]['workshops']);
    }
}
