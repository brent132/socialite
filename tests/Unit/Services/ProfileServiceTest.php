<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Services\ProfileService;
use App\Services\ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $profileService;
    protected $imageService;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create mock dependencies
        $this->imageService = $this->createMock(ImageService::class);
        
        // Create the service with mocked dependencies
        $this->profileService = new ProfileService($this->imageService);
        
        // Create a test user
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_gets_profile_stats()
    {
        // Assert
        $stats = $this->profileService->getProfileStats($this->user);
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('postCount', $stats);
        $this->assertArrayHasKey('followerCount', $stats);
        $this->assertArrayHasKey('followingCount', $stats);
    }

    /** @test */
    public function it_updates_profile_bio()
    {
        // Arrange
        $newBio = 'This is a new test bio';
        
        // Act
        $result = $this->profileService->updateProfileBio($this->user, $newBio);
        
        // Assert
        $this->assertTrue($result);
        $this->assertEquals($newBio, $this->user->profile->fresh()->description);
    }

    /** @test */
    public function it_returns_error_if_no_image_provided_for_profile_picture()
    {
        // Act
        $result = $this->profileService->updateProfilePicture($this->user, null);
        
        // Assert
        $this->assertFalse($result['success']);
        $this->assertEquals('No image provided', $result['message']);
    }

    /** @test */
    public function it_updates_profile_picture()
    {
        // Arrange
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $this->imageService->method('processProfileImage')->willReturn([
            'success' => true,
            'path' => 'profile/test-image.jpg',
            'message' => 'Profile image processed successfully'
        ]);
        
        // Act
        $result = $this->profileService->updateProfilePicture($this->user, $file);
        
        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('profile/test-image.jpg', $this->user->profile->fresh()->image);
    }
} 