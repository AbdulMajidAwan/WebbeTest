<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\MenuItem;
class MenuTest extends TestCase

{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    use DatabaseTransactions;

    public function testGetMenuItems()
    {
        // Create menu items with nested structure
        $menuItem1 = MenuItem::factory()->create(['parent_id' => null, 'order' => 1]);
        $menuItem2 = MenuItem::factory()->create(['parent_id' => null, 'order' => 2]);
        $menuItem3 = MenuItem::factory()->create(['parent_id' => $menuItem1->id, 'order' => 1]);
        $menuItem4 = MenuItem::factory()->create(['parent_id' => $menuItem1->id, 'order' => 2]);
        $menuItem5 = MenuItem::factory()->create(['parent_id' => $menuItem3->id, 'order' => 1]);

        // Call the getMenuItems function
        $response = $this->get('/menu');

        // Assert the response status
        $response->assertStatus(200);

        // Assert the response structure and content
        $response->assertJsonCount(2); // Assert there are two top-level menu items

        $response->assertJsonFragment([
            'id' => $menuItem1->id,
            'parent_id' => null,
            'order' => 1,
            'children' => [
                [
                    'id' => $menuItem3->id,
                    'parent_id' => $menuItem1->id,
                    'order' => 1,
                    'children' => [
                        [
                            'id' => $menuItem5->id,
                            'parent_id' => $menuItem3->id,
                            'order' => 1,
                        ]
                    ]
                ],
                [
                    'id' => $menuItem4->id,
                    'parent_id' => $menuItem1->id,
                    'order' => 2,
                ]
            ]
        ]);

        $response->assertJsonFragment([
            'id' => $menuItem2->id,
            'parent_id' => null,
            'order' => 2,
        ]);
    }
}
