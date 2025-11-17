<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Idea;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_status_can_be_created()
    {
        $status = Status::factory()->create([
            'name' => 'Test Status',
        ]);

        $this->assertDatabaseHas('statuses', [
            'name' => 'Test Status',
        ]);

        $this->assertEquals('Test Status', $status->name);
    }

    /** @test */
    public function test_status_has_many_ideas()
    {
        $status = Status::factory()->create();
        $ideas = Idea::factory()->count(3)->create([
            'status_id' => $status->id,
        ]);

        $this->assertCount(3, $status->ideas);
        $this->assertInstanceOf(Idea::class, $status->ideas->first());
    }

    /** @test */
    public function test_status_get_count_returns_array()
    {
        Status::factory()->create(['id' => 1, 'name' => 'Open']);
        Status::factory()->create(['id' => 2, 'name' => 'Considering']);
        Status::factory()->create(['id' => 3, 'name' => 'In Progress']);
        Status::factory()->create(['id' => 4, 'name' => 'Implemented']);
        Status::factory()->create(['id' => 5, 'name' => 'Closed']);

        Idea::factory()->count(5)->create(['status_id' => 1]);
        Idea::factory()->count(3)->create(['status_id' => 2]);
        Idea::factory()->count(2)->create(['status_id' => 3]);

        $counts = Status::getCount();

        $this->assertIsArray($counts);
        $this->assertArrayHasKey('all_statues', $counts);
        $this->assertArrayHasKey('open', $counts);
        $this->assertEquals(10, $counts['all_statues']);
        $this->assertEquals(5, $counts['open']);
    }

    /** @test */
    public function test_status_name_and_classes_are_fillable()
    {
        $status = new Status();
        $status->fill([
            'name' => 'Fillable Status',
            'classes' => 'bg-blue text-white',
        ]);

        $this->assertEquals('Fillable Status', $status->name);
        $this->assertEquals('bg-blue text-white', $status->classes);
    }

    /** @test */
    public function test_status_can_be_deleted()
    {
        $status = Status::factory()->create();
        $statusId = $status->id;

        $status->delete();

        $this->assertDatabaseMissing('statuses', [
            'id' => $statusId,
        ]);
    }

    /** @test */
    public function test_status_can_be_updated()
    {
        $status = Status::factory()->create([
            'name' => 'Original Status',
        ]);

        $status->update(['name' => 'Updated Status']);

        $this->assertDatabaseHas('statuses', [
            'id' => $status->id,
            'name' => 'Updated Status',
        ]);
    }

    /** @test */
    public function test_status_ideas_relationship_returns_collection()
    {
        $status = Status::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $status->ideas);
    }

    /** @test */
    public function test_status_get_count_with_no_ideas()
    {
        Status::factory()->create(['id' => 1, 'name' => 'Open']);
        Status::factory()->create(['id' => 2, 'name' => 'Considering']);
        Status::factory()->create(['id' => 3, 'name' => 'In Progress']);
        Status::factory()->create(['id' => 4, 'name' => 'Implemented']);
        Status::factory()->create(['id' => 5, 'name' => 'Closed']);

        $counts = Status::getCount();

        $this->assertEquals(0, $counts['all_statues']);
        $this->assertEquals(0, $counts['open']);
    }
}
