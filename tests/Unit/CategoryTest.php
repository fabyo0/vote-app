<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Idea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_category_can_be_created()
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
        ]);

        $this->assertEquals('Test Category', $category->name);
    }

    /** @test */
    public function test_category_has_many_ideas()
    {
        $category = Category::factory()->create();
        $ideas = Idea::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $this->assertCount(3, $category->ideas);
        $this->assertInstanceOf(Idea::class, $category->ideas->first());
    }

    /** @test */
    public function test_category_name_is_fillable()
    {
        $category = new Category();
        $category->fill(['name' => 'Fillable Category']);

        $this->assertEquals('Fillable Category', $category->name);
    }

    /** @test */
    public function test_category_can_be_deleted()
    {
        $category = Category::factory()->create();
        $categoryId = $category->id;

        $category->delete();

        $this->assertDatabaseMissing('categories', [
            'id' => $categoryId,
        ]);
    }

    /** @test */
    public function test_category_can_be_updated()
    {
        $category = Category::factory()->create([
            'name' => 'Original Name',
        ]);

        $category->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function test_multiple_categories_can_exist()
    {
        Category::factory()->count(5)->create();

        $this->assertEquals(5, Category::count());
    }

    /** @test */
    public function test_category_ideas_relationship_returns_collection()
    {
        $category = Category::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $category->ideas);
    }
}