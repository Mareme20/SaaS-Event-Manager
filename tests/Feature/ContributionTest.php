<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Contribution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContributionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_contribution_to_event(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);
        $contributor = User::factory()->create();

        $response = $this->actingAs($user)->post(route('contributions.store', $event->id), [
            'user_id' => $contributor->id,
            'amount' => 5000,
            'description' => 'Cotisation test',
            'date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('contributions', [
            'event_id' => $event->id,
            'user_id' => $contributor->id,
            'amount' => 5000,
        ]);
    }

    public function test_user_can_delete_contribution(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);
        $contribution = Contribution::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'amount' => 1000,
            'date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->delete(route('contributions.destroy', $contribution->id));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('contributions', ['id' => $contribution->id]);
    }
}
