<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_follow_another_user()
    {
        // Arrange
        $user = User::factory()->create();
        $userToFollow = User::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->post("/follow/{$userToFollow->id}");

        // Assert
        $response->assertStatus(302); // Redirect
        $this->assertTrue($user->following->contains($userToFollow->id));
    }

    /** @test */
    public function authenticated_user_can_unfollow_another_user()
    {
        // Arrange
        $user = User::factory()->create();
        $userToUnfollow = User::factory()->create();
        
        // First follow the user
        $user->following()->attach($userToUnfollow->id);
        
        // Act
        $response = $this->actingAs($user)
            ->post("/unfollow/{$userToUnfollow->id}");
            
        // Assert
        $response->assertStatus(302); // Redirect
        $this->assertFalse($user->following()->where('followed_id', $userToUnfollow->id)->exists());
    }

    /** @test */
    public function user_cannot_follow_themselves()
    {
        // Arrange
        $user = User::factory()->create();
        
        // Act
        $response = $this->actingAs($user)
            ->post("/follow/{$user->id}");
            
        // Assert
        $response->assertStatus(302); // Redirect
        $this->assertFalse($user->following->contains($user->id));
    }

    /** @test */
    public function unauthenticated_user_cannot_follow_others()
    {
        // Arrange
        $userToFollow = User::factory()->create();
        
        // Act
        $response = $this->post("/follow/{$userToFollow->id}");
        
        // Assert
        $response->assertRedirect('/login');
    }
} 