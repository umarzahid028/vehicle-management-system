<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FixNotificationSound extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:notification-sound {--source-url= : URL of a new notification sound file to download}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix notification sound issues by downloading a valid MP3 file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Fixing notification sound...");
        
        // Create the audio directory if it doesn't exist
        $audioDir = public_path('audio');
        if (!File::exists($audioDir)) {
            File::makeDirectory($audioDir, 0755, true);
            $this->info("Created audio directory: {$audioDir}");
        }
        
        // Path to notification sound file
        $soundFilePath = public_path('audio/notification.mp3');
        
        // Check if file exists and its size
        if (File::exists($soundFilePath)) {
            $fileSize = File::size($soundFilePath);
            $this->info("Current notification sound file size: {$fileSize} bytes");
            
            if ($fileSize < 1000) {
                $this->warn("The current notification sound file is very small, which may cause issues");
            }
        } else {
            $this->warn("Notification sound file not found at: {$soundFilePath}");
        }
        
        // Check if we need to download a new sound file
        $sourceUrl = $this->option('source-url') ?: 'https://cdn.pixabay.com/audio/2021/08/04/audio_0625c1539c.mp3';
        
        if (!empty($sourceUrl)) {
            $this->info("Downloading new notification sound from: {$sourceUrl}");
            
            try {
                // Download the file
                $newSoundData = file_get_contents($sourceUrl);
                if ($newSoundData === false) {
                    throw new \Exception("Failed to download file from URL");
                }
                
                // Save the file
                if (File::put($soundFilePath, $newSoundData)) {
                    $newFileSize = File::size($soundFilePath);
                    $this->info("Successfully downloaded and saved new notification sound. File size: {$newFileSize} bytes");
                    
                    // Set correct permissions
                    chmod($soundFilePath, 0644);
                    
                    // Create a test HTML file
                    $this->createTestFile();
                    
                    return 0;
                } else {
                    throw new \Exception("Failed to save file");
                }
            } catch (\Exception $e) {
                $this->error("Error downloading sound file: " . $e->getMessage());
                Log::error("Failed to download notification sound: " . $e->getMessage());
                return 1;
            }
        }
        
        $this->info("No action taken. Use --source-url option to specify a new sound file URL.");
        return 0;
    }
    
    /**
     * Create a simple HTML file for testing audio playback
     */
    protected function createTestFile()
    {
        $testFilePath = public_path('audio-test.html');
        $testFileContent = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Audio Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: 0 auto; }
        button { padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; margin: 5px; }
        .container { border: 1px solid #ddd; padding: 20px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Notification Sound Test</h1>
    <p>Click the button below to play the notification sound.</p>
    
    <button id="play-btn">Play Sound</button>
    <button id="init-audio">Initialize Audio (Try this first if sound doesn't play)</button>
    
    <div class="container">
        <p>Status: <span id="status">Ready</span></p>
    </div>
    
    <audio id="notification" src="/audio/notification.mp3" preload="auto"></audio>
    
    <script>
        const audio = document.getElementById('notification');
        const playBtn = document.getElementById('play-btn');
        const initBtn = document.getElementById('init-audio');
        const status = document.getElementById('status');
        
        // Initialize audio (needed for browsers with strict autoplay policies)
        initBtn.addEventListener('click', function() {
            status.textContent = 'Initializing...';
            
            // Create audio context
            try {
                window.AudioContext = window.AudioContext || window.webkitAudioContext;
                const audioContext = new AudioContext();
                
                // Play and immediately pause to "wake up" the audio system
                audio.play().then(() => {
                    audio.pause();
                    audio.currentTime = 0;
                    status.textContent = 'Audio initialized successfully!';
                }).catch(error => {
                    status.textContent = 'Error initializing audio: ' + error.message;
                });
            } catch (error) {
                status.textContent = 'Error creating audio context: ' + error.message;
            }
        });
        
        // Play button
        playBtn.addEventListener('click', function() {
            status.textContent = 'Playing...';
            
            // Reset and play
            audio.pause();
            audio.currentTime = 0;
            
            audio.play().then(() => {
                status.textContent = 'Sound played successfully!';
            }).catch(error => {
                status.textContent = 'Error playing sound: ' + error.message + ' (Try initializing first)';
            });
        });
    </script>
</body>
</html>
HTML;
        
        File::put($testFilePath, $testFileContent);
        $this->info("Created test HTML file at " . url('audio-test.html'));
    }
} 