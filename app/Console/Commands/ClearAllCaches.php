<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearAllCaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all caches (application, config, route, view)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing all caches...');
        
        // Clear application cache
        $this->info('Clearing application cache...');
        Artisan::call('cache:clear');
        $this->info('Application cache cleared!');
        
        // Clear config cache
        $this->info('Clearing config cache...');
        Artisan::call('config:clear');
        $this->info('Config cache cleared!');
        
        // Clear route cache
        $this->info('Clearing route cache...');
        Artisan::call('route:clear');
        $this->info('Route cache cleared!');
        
        // Clear view cache
        $this->info('Clearing view cache...');
        Artisan::call('view:clear');
        $this->info('View cache cleared!');
        
        // Clear compiled
        $this->info('Clearing compiled files...');
        Artisan::call('clear-compiled');
        $this->info('Compiled files cleared!');
        
        $this->info('All caches have been cleared successfully!');
        
        return Command::SUCCESS;
    }
}
