<?php

declare(strict_types=1);

namespace Tests\Integration;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ScraperStoreTest extends TestCase
{
    use DatabaseMigrations;

    public function testWhenPageIsInvalid(): void
    {
        $response = $this->post('/scrape', [
            'page' => 'definitely-not-valid'
        ]);

        $response->assertStatus(422)
        ->assertJson([
            'status'  => 'The given data was invalid.',
            'message' => ['The page must be an integer.'],
        ]);
    }

    public function testReturnsNews(): void
    {
        $response = $this->post('/scrape')
        ->assertStatus(200);

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('news', $data);
        $this->assertGreaterThan(0, count($data['news']));

        $this->assertDatabaseHas('news', [
            'id' => $data['news'][0]['id']
        ]);
    }
}
