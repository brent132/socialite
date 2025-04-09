<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Illuminate\Support\Facades\Cache;

class ImageService
{
    protected $manager;
    protected $defaultOptions;

    public function __construct()
    {
        // Initialize manager with optimized settings
        $this->manager = new ImageManager(new Driver());
        
        // Default optimization options for images
        $this->defaultOptions = [
            'jpg' => ['quality' => 80],
            'png' => ['compression_level' => 7],
            'webp' => ['quality' => 80]
        ];
    }

    /**
     * Process and store a profile image
     * 
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $oldImagePath
     * @return array
     */
    public function processProfileImage($image, $oldImagePath = null)
    {
        try {
            // Generate a unique cache key for this image processing operation
            $cacheKey = 'profile_image_' . md5($image->getClientOriginalName() . $image->getSize() . time());
            
            return Cache::remember($cacheKey, now()->addMinutes(10), function() use ($image, $oldImagePath) {
                // Delete old image if exists
                if ($oldImagePath) {
                    $this->removeImage($oldImagePath);
                }

                // Get file extension and prepare for WebP conversion if supported
                $extension = $image->getClientOriginalExtension();
                $outputFormat = $this->getSupportedFormat($extension);
                
                // Store the new image with a path that reflects the format
                $filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $imagePath = "profile/" . $filename . "_" . time() . "." . $outputFormat;
                
                // Create optimized version
                $imageStream = $this->manager->read($image->getRealPath());
                $imageStream->scale(width: 400, height: 400);
                
                // Apply optimization based on format
                $encodedImage = $this->encodeOptimized($imageStream, $outputFormat);
                
                // Store directly without saving to disk first to minimize I/O
                Storage::disk('public')->put($imagePath, $encodedImage);
                
                return [
                    'success' => true,
                    'path' => $imagePath,
                    'message' => 'Profile image processed successfully'
                ];
            });
        } catch (\Exception $exception) {
            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            
            return [
                'success' => false,
                'path' => null,
                'message' => 'Failed to process profile image: ' . $exception->getMessage()
            ];
        }
    }

    /**
     * Process and store a background image
     * 
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $oldImagePath
     * @return array
     */
    public function processBackgroundImage($image, $oldImagePath = null)
    {
        try {
            // Generate a unique cache key for this image processing operation
            $cacheKey = 'background_image_' . md5($image->getClientOriginalName() . $image->getSize() . time());
            
            return Cache::remember($cacheKey, now()->addMinutes(10), function() use ($image, $oldImagePath) {
                // Delete old image if exists
                if ($oldImagePath) {
                    $this->removeImage($oldImagePath);
                }

                // Get file extension and prepare for WebP conversion if supported
                $extension = $image->getClientOriginalExtension();
                $outputFormat = $this->getSupportedFormat($extension);
                
                // Store the new image with a path that reflects the format
                $filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $imagePath = "profile/backgrounds/" . $filename . "_" . time() . "." . $outputFormat;
                
                // Create optimized version
                $imageStream = $this->manager->read($image->getRealPath());
                $imageStream->scale(width: 1920, height: 1080);
                
                // Apply optimization based on format
                $encodedImage = $this->encodeOptimized($imageStream, $outputFormat);
                
                // Store directly without saving to disk first to minimize I/O
                Storage::disk('public')->put($imagePath, $encodedImage);
                
                return [
                    'success' => true,
                    'path' => $imagePath,
                    'message' => 'Background image processed successfully'
                ];
            });
        } catch (\Exception $exception) {
            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            
            return [
                'success' => false,
                'path' => null,
                'message' => 'Failed to process background image: ' . $exception->getMessage()
            ];
        }
    }

    /**
     * Remove a stored image
     * 
     * @param string $imagePath
     * @return bool
     */
    public function removeImage($imagePath)
    {
        if ($imagePath) {
            return Storage::disk('public')->delete($imagePath);
        }
        
        return false;
    }
    
    /**
     * Get the best supported output format for the image
     * 
     * @param string $originalExtension
     * @return string
     */
    protected function getSupportedFormat($originalExtension)
    {
        // Prefer WebP for better compression and quality
        // if supported by the browser (most modern browsers do)
        if (in_array(strtolower($originalExtension), ['jpg', 'jpeg', 'png'])) {
            return 'webp';
        }
        
        return strtolower($originalExtension);
    }
    
    /**
     * Encode image with optimized settings
     * 
     * @param \Intervention\Image\Interfaces\ImageInterface $image
     * @param string $format
     * @return string
     */
    protected function encodeOptimized($image, $format)
    {
        $options = $this->defaultOptions[$format] ?? [];
        
        if ($format === 'webp') {
            return $image->encodeWebp($options['quality'])->toString();
        } elseif ($format === 'jpg' || $format === 'jpeg') {
            return $image->encodeJpeg($options['quality'])->toString();
        } elseif ($format === 'png') {
            return $image->encodePng($options['compression_level'])->toString();
        }
        
        // Default fallback encoding
        return $image->encode()->toString();
    }

    /**
     * Ensure image directories exist
     * 
     * @return void
     */
    public function ensureImageDirectoriesExist()
    {
        $directories = [
            'profile',
            'profile/backgrounds',
            'posts',
        ];
        
        foreach ($directories as $directory) {
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }
        }
        
        // Ensure default profile image exists
        if (!Storage::disk('public')->exists('profile/default-avatar.png')) {
            // Copy from public/images if available
            if (file_exists(public_path('images/profile.png'))) {
                $defaultImage = file_get_contents(public_path('images/profile.png'));
                Storage::disk('public')->put('profile/default-avatar.png', $defaultImage);
            }
        }
    }
} 