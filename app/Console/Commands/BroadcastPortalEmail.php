<?php

namespace App\Console\Commands;

use App\Mail\PortalPromotionalEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BroadcastPortalEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal:broadcast-email {file : Filename of the Excel file (e.g., customers.xlsx)}';

    /**
     * The folder where Excel files should be placed
     */
    private string $storageFolder = 'broadcast-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast promotional emails to customers from an Excel file advertising the portal';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filename = $this->argument('file');
        $storagePath = storage_path('app/'.$this->storageFolder);

        // Create directory if it doesn't exist
        if (! is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
            $this->info("Created directory: {$storagePath}");
        }

        $filePath = $storagePath.'/'.$filename;

        // Validate file exists
        if (! file_exists($filePath)) {
            $this->error("File not found: {$filename}");
            $this->newLine();
            $this->info('Please place your Excel file in:');
            $this->line("  📁 {$storagePath}");
            $this->newLine();
            $this->info('Expected file format:');
            $this->line('  📄 Filename: customers.xlsx (or customers.csv)');
            $this->line('  📋 Columns: name, surname, email');
            $this->newLine();
            $this->info('Example file structure:');
            $this->line('  | name    | surname  | email                |');
            $this->line('  |---------|----------|----------------------|');
            $this->line('  | John    | Doe      | john.doe@email.com   |');
            $this->line('  | Jane    | Smith    | jane.smith@email.com |');

            return Command::FAILURE;
        }

        // Read customer data from Excel file
        $customers = $this->readCustomersFromFile($filePath);

        if (empty($customers)) {
            $this->error('No valid customer data found in the file. File must have name, surname, and email columns.');

            return Command::FAILURE;
        }

        $this->info('Found '.count($customers).' customers to send emails to.');
        $this->newLine();

        // Confirm before sending
        if (! $this->confirm('Do you want to proceed with sending emails?', true)) {
            $this->info('Operation cancelled.');

            return Command::SUCCESS;
        }

        // Send emails
        $sentCount = 0;
        $failedCount = 0;
        $failedEmails = [];

        $bar = $this->output->createProgressBar(count($customers));
        $bar->start();

        foreach ($customers as $customer) {
            try {
                // Validate email format
                $validator = Validator::make(['email' => $customer['email']], [
                    'email' => 'required|email',
                ]);

                if ($validator->fails()) {
                    $failedCount++;
                    $failedEmails[] = $customer['email'];
                    $bar->advance();

                    continue;
                }

                // Send personalized email
                Mail::to($customer['email'])->send(
                    new PortalPromotionalEmail($customer['name'], $customer['surname'])
                );
                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $failedEmails[] = $customer['email'];
                $this->newLine();
                $this->warn("Failed to send to {$customer['email']}: ".$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Display results
        $this->info('Email broadcast completed!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Sent', $sentCount],
                ['Failed', $failedCount],
                ['Total', count($customers)],
            ]
        );

        if (! empty($failedEmails)) {
            $this->newLine();
            $this->warn('Failed emails:');
            foreach ($failedEmails as $failedEmail) {
                $this->line("  - {$failedEmail}");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Read customer data (name, surname, email) from Excel or CSV file
     */
    private function readCustomersFromFile(string $filePath): array
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $customers = [];

        if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
            // Handle Excel files
            if (! class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                $this->error('Excel file support requires PhpSpreadsheet package.');
                $this->newLine();
                $this->info('To install PhpSpreadsheet, run:');
                $this->line('  composer require phpoffice/phpspreadsheet');
                $this->newLine();
                $this->info('Alternatively, convert your Excel file to CSV format:');
                $this->line('  1. Open your Excel file');
                $this->line('  2. Go to File > Save As');
                $this->line('  3. Choose CSV (Comma delimited) format');
                $this->line('  4. Save the file with .csv extension');
                $this->newLine();

                return [];
            }

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                // Find header row
                $headerRow = [];
                $headerRowNum = 0;
                $nameColumn = null;
                $surnameColumn = null;
                $emailColumn = null;

                for ($row = 1; $row <= min(10, $highestRow); $row++) {
                    $firstCell = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    if ($firstCell && (stripos($firstCell, 'email') !== false || stripos($firstCell, 'name') !== false)) {
                        $headerRowNum = $row;
                        // Get header row and find columns
                        for ($col = 1; $col <= 20; $col++) {
                            $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            if ($cellValue) {
                                $headerValue = strtolower(trim($cellValue));
                                if (stripos($headerValue, 'email') !== false) {
                                    $emailColumn = $col;
                                } elseif (stripos($headerValue, 'surname') !== false || stripos($headerValue, 'lastname') !== false) {
                                    $surnameColumn = $col;
                                } elseif (stripos($headerValue, 'name') !== false && $surnameColumn === null) {
                                    $nameColumn = $col;
                                }
                            }
                        }
                        break;
                    }
                }

                // Collect all found headers for better error reporting
                $foundHeaders = [];
                if ($headerRowNum > 0) {
                    for ($col = 1; $col <= 20; $col++) {
                        $cellValue = $worksheet->getCellByColumnAndRow($col, $headerRowNum)->getValue();
                        if ($cellValue) {
                            $foundHeaders[] = trim($cellValue);
                        }
                    }
                }

                if (! $emailColumn) {
                    $this->error('Could not find email column in Excel file.');
                    $this->newLine();
                    $this->info('Found columns: '.($foundHeaders ? implode(', ', $foundHeaders) : 'None'));
                    $this->info('Please ensure your file has a column named "email" (case-insensitive).');

                    return [];
                }

                if (! $nameColumn) {
                    $this->error('Could not find name column in Excel file.');
                    $this->newLine();
                    $this->info('Found columns: '.($foundHeaders ? implode(', ', $foundHeaders) : 'None'));
                    $this->info('Please ensure your file has a column named "name" (case-insensitive).');

                    return [];
                }

                if (! $surnameColumn) {
                    $this->error('Could not find surname column in Excel file.');
                    $this->newLine();
                    $this->info('Found columns: '.($foundHeaders ? implode(', ', $foundHeaders) : 'None'));
                    $this->info('Please ensure your file has a column named "surname" or "lastname" (case-insensitive).');

                    return [];
                }

                // Process data rows
                $processedCount = 0;
                for ($row = $headerRowNum + 1; $row <= $highestRow; $row++) {
                    $emailValue = $worksheet->getCellByColumnAndRow($emailColumn, $row)->getValue();
                    $nameValue = $worksheet->getCellByColumnAndRow($nameColumn, $row)->getValue();
                    $surnameValue = $worksheet->getCellByColumnAndRow($surnameColumn, $row)->getValue();

                    // Skip empty rows
                    if (empty($emailValue) && empty($nameValue) && empty($surnameValue)) {
                        continue;
                    }

                    $emailValue = trim($emailValue ?? '');
                    $nameValue = trim($nameValue ?? '');
                    $surnameValue = trim($surnameValue ?? '');

                    if ($emailValue && filter_var($emailValue, FILTER_VALIDATE_EMAIL)) {
                        $customers[] = [
                            'name' => $nameValue,
                            'surname' => $surnameValue,
                            'email' => $emailValue,
                        ];
                        $processedCount++;
                    }
                }

                if ($processedCount === 0) {
                    $this->warn('No valid customer records found in Excel file. Please check that:');
                    $this->line('  1. Data rows exist after the header row');
                    $this->line('  2. Email addresses are valid');
                    $this->line('  3. At least one row has data');
                }
            } catch (\Exception $e) {
                $this->error('Error reading Excel file: '.$e->getMessage());

                return [];
            }
        } else {
            // Handle CSV files
            $file = fopen($filePath, 'r');
            if ($file === false) {
                $this->error('Failed to open CSV file.');

                return [];
            }

            try {
                // Read header row (first row)
                $headerRow = fgetcsv($file);
                if ($headerRow === false || empty($headerRow)) {
                    $this->error('CSV file is empty or could not read header row.');
                    fclose($file);

                    return [];
                }

                // Find column indices
                $nameColumnIndex = null;
                $surnameColumnIndex = null;
                $emailColumnIndex = null;
                $foundHeaders = [];

                foreach ($headerRow as $index => $cell) {
                    $cellValue = trim($cell);
                    if ($cellValue) {
                        $foundHeaders[] = $cellValue;
                        $cellValueLower = strtolower($cellValue);

                        // Check for email column
                        if (stripos($cellValueLower, 'email') !== false) {
                            $emailColumnIndex = $index;
                        }
                        // Check for surname column (prioritize surname/lastname over just "name")
                        elseif (stripos($cellValueLower, 'surname') !== false ||
                                stripos($cellValueLower, 'lastname') !== false ||
                                stripos($cellValueLower, 'last name') !== false) {
                            $surnameColumnIndex = $index;
                        }
                        // Check for name column (but not if it's surname/lastname)
                        elseif (stripos($cellValueLower, 'name') !== false &&
                                stripos($cellValueLower, 'surname') === false &&
                                stripos($cellValueLower, 'lastname') === false &&
                                stripos($cellValueLower, 'last name') === false &&
                                stripos($cellValueLower, 'email') === false) {
                            // Only set if not already set (to avoid overwriting)
                            if ($nameColumnIndex === null) {
                                $nameColumnIndex = $index;
                            }
                        }
                    }
                }

                // Validate all required columns found
                if ($emailColumnIndex === null || $nameColumnIndex === null || $surnameColumnIndex === null) {
                    $this->error('Could not find all required columns in CSV file.');
                    $this->newLine();
                    $this->info('Found columns: '.($foundHeaders ? implode(', ', $foundHeaders) : 'None'));
                    $this->newLine();
                    $this->info('Required columns:');
                    $this->line('  - email (found: '.($emailColumnIndex !== null ? 'Yes' : 'No').')');
                    $this->line('  - name (found: '.($nameColumnIndex !== null ? 'Yes' : 'No').')');
                    $this->line('  - surname or lastname (found: '.($surnameColumnIndex !== null ? 'Yes' : 'No').')');
                    $this->newLine();
                    $this->info('Please ensure your CSV file has these columns in the first row.');
                    fclose($file);

                    return [];
                }

                // Read data rows
                $processedCount = 0;
                while (($row = fgetcsv($file)) !== false) {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Ensure row has enough columns
                    $maxIndex = max($emailColumnIndex, $nameColumnIndex, $surnameColumnIndex);
                    if (count($row) <= $maxIndex) {
                        continue;
                    }

                    $emailValue = trim($row[$emailColumnIndex] ?? '');
                    $nameValue = trim($row[$nameColumnIndex] ?? '');
                    $surnameValue = trim($row[$surnameColumnIndex] ?? '');

                    // Validate email and add to customers
                    if ($emailValue && filter_var($emailValue, FILTER_VALIDATE_EMAIL)) {
                        $customers[] = [
                            'name' => $nameValue,
                            'surname' => $surnameValue,
                            'email' => $emailValue,
                        ];
                        $processedCount++;
                    }
                }

                if ($processedCount === 0) {
                    $this->warn('No valid customer records found in CSV file. Please check that:');
                    $this->line('  1. Data rows exist after the header row');
                    $this->line('  2. Email addresses are valid');
                    $this->line('  3. At least one row has data');
                }
            } finally {
                fclose($file);
            }
        }

        // Remove duplicates based on email
        $uniqueCustomers = [];
        $seenEmails = [];
        foreach ($customers as $customer) {
            if (! in_array($customer['email'], $seenEmails)) {
                $uniqueCustomers[] = $customer;
                $seenEmails[] = $customer['email'];
            }
        }

        return $uniqueCustomers;
    }
}
