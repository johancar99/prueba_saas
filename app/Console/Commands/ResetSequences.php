<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetSequences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset-sequences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset auto-increment sequences for all tables to start from 1';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting auto-increment sequences for MySQL...');

        $tables = [
            'users',
            'companies', 
            'plans',
            'subscriptions',
            'personal_access_tokens',
            'roles',
            'permissions'
        ];

        foreach ($tables as $table) {
            try {
                $maxId = DB::table($table)->max('id') ?? 0;
                
                // Para MySQL, usamos ALTER TABLE para resetear el auto_increment
                DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = " . ($maxId + 1));
                
                $this->info("✓ Reset auto_increment for table: {$table} (max id: {$maxId})");
            } catch (\Exception $e) {
                $this->warn("⚠ Could not reset auto_increment for table: {$table} - {$e->getMessage()}");
            }
        }

        $this->info('Auto-increment sequences reset completed!');
        
        // Mostrar información de los auto_increment actuales
        $this->info('Current auto_increment values:');
        foreach ($tables as $table) {
            try {
                $result = DB::select("SHOW TABLE STATUS LIKE '{$table}'");
                if (!empty($result)) {
                    $autoIncrement = $result[0]->Auto_increment ?? 'N/A';
                    $this->line("  {$table}: {$autoIncrement}");
                } else {
                    $this->line("  {$table}: Table not found");
                }
            } catch (\Exception $e) {
                $this->line("  {$table}: Error getting auto_increment value");
            }
        }
    }
} 