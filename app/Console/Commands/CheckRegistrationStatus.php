<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Customerprofession;
use App\Models\Customerregistration;
use Illuminate\Console\Command;

class CheckRegistrationStatus extends Command
{
    protected $signature = 'check:registration {name?}';
    protected $description = 'Check registration status for a customer';

    public function handle()
    {
        $name = $this->argument('name') ?? 'Tonodzai';
        
        $this->info("Searching for customers matching: {$name}");
        $this->newLine();
        
        // Find customers
        $customers = Customer::where('name', 'like', "%{$name}%")
            ->orWhere('surname', 'like', "%{$name}%")
            ->get();
        
        if ($customers->isEmpty()) {
            $this->error("No customers found matching '{$name}'");
            return 1;
        }
        
        foreach ($customers as $customer) {
            $this->info("=== Customer: {$customer->name} {$customer->surname} (ID: {$customer->id}) ===");
            
            // Get professions
            $professions = Customerprofession::where('customer_id', $customer->id)
                ->with(['profession', 'registration'])
                ->get();
            
            if ($professions->isEmpty()) {
                $this->warn("  No professions found");
                continue;
            }
            
            foreach ($professions as $prof) {
                $this->line("  Profession: " . ($prof->profession->name ?? 'N/A'));
                $this->line("    - ID: {$prof->id}");
                $this->line("    - UUID: {$prof->uuid}");
                $this->line("    - Status: {$prof->status}");
                $this->line("    - Year: {$prof->year}");
                $this->line("    - Updated: {$prof->updated_at->format('Y-m-d H:i:s')}");
                
                // Check registration
                if ($prof->registration) {
                    $this->line("    - Registration Status: {$prof->registration->status}");
                    $this->line("    - Registration Year: {$prof->registration->year}");
                } else {
                    $this->warn("    - NO REGISTRATION RECORD");
                }
                
                // Check if should appear in approvals
                $currentYear = date('Y');
                if ($prof->status === 'AWAITING_REG' && $prof->year == $currentYear) {
                    $this->info("    ✓ SHOULD APPEAR IN REGISTRATION APPROVALS");
                } else {
                    $this->error("    ✗ Will NOT appear in approvals");
                    if ($prof->status !== 'AWAITING_REG') {
                        $this->warn("      Reason: Status is '{$prof->status}' (expected 'AWAITING_REG')");
                    }
                    if ($prof->year != $currentYear) {
                        $this->warn("      Reason: Year is '{$prof->year}' (expected '{$currentYear}')");
                    }
                }
                
                $this->newLine();
            }
        }
        
        // Summary of all AWAITING_REG
        $this->info("=== ALL AWAITING_REG REGISTRATIONS ===");
        $awaiting = Customerprofession::where('status', 'AWAITING_REG')
            ->where('year', date('Y'))
            ->with(['customer', 'profession'])
            ->get();
        
        if ($awaiting->isEmpty()) {
            $this->warn("No AWAITING_REG registrations found for current year");
        } else {
            $this->info("Found {$awaiting->count()} AWAITING_REG registrations:");
            foreach ($awaiting as $prof) {
                $this->line("  - {$prof->customer->name} {$prof->customer->surname} ({$prof->profession->name ?? 'N/A'})");
            }
        }
        
        return 0;
    }
}
