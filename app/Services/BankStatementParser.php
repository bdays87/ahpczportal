<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BankStatementParser
{
    /**
     * Parse bank statement based on detected format
     */
    public function parse(string $filePath, string $bankType): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        if ($extension === 'csv') {
            return $this->parseCSV($filePath, $bankType);
        } elseif (in_array($extension, ['xls', 'xlsx'])) {
            return $this->parseExcel($filePath, $bankType);
        }
        
        throw new \Exception('Unsupported file format');
    }

    /**
     * Parse CSV files
     */
    private function parseCSV(string $filePath, string $bankType): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }
        
        $file = fopen($filePath, 'r');
        $transactions = [];
        $metadata = [];
        
        switch ($bankType) {
            case 'NMB':
                $transactions = $this->parseNMBFormat($file, $metadata);
                break;
            case 'FCB':
            case 'FirstCapital':
                $transactions = $this->parseFCBFormat($file, $metadata);
                break;
            default:
                $transactions = $this->parseGenericCSV($file, $metadata);
        }
        
        fclose($file);
        
        return [
            'transactions' => $transactions,
            'metadata' => $metadata,
        ];
    }

    /**
     * Parse NMB Bank statement format
     */
    private function parseNMBFormat($file, &$metadata): array
    {
        $transactions = [];
        $lineNumber = 0;
        
        while (($row = fgetcsv($file, null, ',')) !== false) {
            $lineNumber++;
            
            // Skip empty lines
            if (empty(array_filter($row))) {
                continue;
            }
            
            // Extract metadata from header
            if ($lineNumber === 1 && stripos($row[0], 'ACCOUNT NAME') !== false) {
                $metadata['account_name'] = trim($row[1] ?? '');
                continue;
            }
            
            if ($lineNumber === 2 && stripos($row[0], 'ACCOUNT NUMBER') !== false) {
                $metadata['account_number'] = trim($row[1] ?? '');
                continue;
            }
            
            // Skip header row
            if (stripos($row[0], 'Transaction Date') !== false) {
                continue;
            }
            
            // Parse transaction rows
            if (!empty($row[0]) && $this->isValidDate($row[0])) {
                try {
                    $debit = $this->parseAmount($row[4] ?? '0');
                    $credit = $this->parseAmount($row[5] ?? '0');
                    $amount = $credit > 0 ? $credit : -$debit;
                    
                    $transactions[] = [
                        'transaction_date' => $this->parseDate($row[0]),
                        'value_date' => $this->parseDate($row[1] ?? $row[0]),
                        'description' => trim($row[2] ?? ''),
                        'reference' => trim($row[3] ?? ''),
                        'debit' => $debit,
                        'credit' => $credit,
                        'amount' => $amount,
                        'balance' => $this->parseAmount($row[6] ?? '0'),
                        'type' => $amount > 0 ? 'CREDIT' : 'DEBIT',
                    ];
                } catch (\Exception $e) {
                    Log::warning("Failed to parse NMB transaction at line {$lineNumber}: " . $e->getMessage());
                }
            }
        }
        
        return $transactions;
    }

    /**
     * Parse First Capital Bank statement format
     */
    private function parseFCBFormat($file, &$metadata): array
    {
        $transactions = [];
        $lineNumber = 0;
        
        while (($row = fgetcsv($file, null, ',')) !== false) {
            $lineNumber++;
            
            // Skip empty lines
            if (empty(array_filter($row))) {
                continue;
            }
            
            // Extract metadata
            if ($lineNumber === 1) {
                $metadata['account_number'] = trim($row[0] ?? '');
                $metadata['account_name'] = trim($row[1] ?? '');
                continue;
            }
            
            if (stripos($row[0] ?? '', 'Currency') !== false) {
                $metadata['currency'] = trim($row[1] ?? '');
                continue;
            }
            
            if (stripos($row[0] ?? '', 'Opening Balance') !== false) {
                $metadata['opening_balance'] = $this->parseAmount($row[1] ?? '0');
                continue;
            }
            
            if (stripos($row[0] ?? '', 'Closing Balance') !== false) {
                $metadata['closing_balance'] = $this->parseAmount($row[1] ?? '0');
                continue;
            }
            
            // Skip header row
            if (stripos($row[0] ?? '', 'Transaction Date') !== false) {
                continue;
            }
            
            // Parse transaction rows
            if (!empty($row[0]) && $this->isValidDate($row[0])) {
                try {
                    $debit = $this->parseAmount($row[4] ?? '0');
                    $credit = $this->parseAmount($row[5] ?? '0');
                    $amount = $credit > 0 ? $credit : -$debit;
                    
                    $transactions[] = [
                        'transaction_date' => $this->parseDate($row[0]),
                        'value_date' => $this->parseDate($row[1] ?? $row[0]),
                        'description' => trim($row[2] ?? ''),
                        'reference' => trim($row[3] ?? ''),
                        'debit' => $debit,
                        'credit' => $credit,
                        'amount' => $amount,
                        'balance' => $this->parseAmount($row[6] ?? '0'),
                        'type' => $amount > 0 ? 'CREDIT' : 'DEBIT',
                    ];
                } catch (\Exception $e) {
                    Log::warning("Failed to parse FCB transaction at line {$lineNumber}: " . $e->getMessage());
                }
            }
        }
        
        return $transactions;
    }

    /**
     * Parse generic CSV format (flexible parser)
     */
    private function parseGenericCSV($file, &$metadata): array
    {
        $transactions = [];
        $headers = [];
        $lineNumber = 0;
        
        while (($row = fgetcsv($file, null, ',')) !== false) {
            $lineNumber++;
            
            // First row as headers
            if ($lineNumber === 1) {
                $headers = array_map('strtolower', array_map('trim', $row));
                continue;
            }
            
            // Skip empty lines
            if (empty(array_filter($row))) {
                continue;
            }
            
            $rowData = array_combine($headers, $row);
            
            if (!empty($rowData['transaction date'] ?? $rowData['date'] ?? '')) {
                try {
                    $debit = $this->parseAmount($rowData['debit'] ?? $rowData['debits'] ?? '0');
                    $credit = $this->parseAmount($rowData['credit'] ?? $rowData['credits'] ?? '0');
                    $amount = $credit > 0 ? $credit : -$debit;
                    
                    $transactions[] = [
                        'transaction_date' => $this->parseDate($rowData['transaction date'] ?? $rowData['date'] ?? ''),
                        'value_date' => $this->parseDate($rowData['value date'] ?? $rowData['transaction date'] ?? $rowData['date'] ?? ''),
                        'description' => trim($rowData['description'] ?? $rowData['narration'] ?? $rowData['particulars'] ?? ''),
                        'reference' => trim($rowData['reference'] ?? $rowData['ref. no.'] ?? $rowData['reference number'] ?? ''),
                        'debit' => $debit,
                        'credit' => $credit,
                        'amount' => $amount,
                        'balance' => $this->parseAmount($rowData['balance'] ?? $rowData['running balance'] ?? '0'),
                        'type' => $amount > 0 ? 'CREDIT' : 'DEBIT',
                    ];
                } catch (\Exception $e) {
                    Log::warning("Failed to parse generic CSV transaction at line {$lineNumber}: " . $e->getMessage());
                }
            }
        }
        
        return $transactions;
    }

    /**
     * Parse Excel files (basic implementation, requires PhpSpreadsheet)
     */
    private function parseExcel(string $filePath, string $bankType): array
    {
        // This would require PhpSpreadsheet library
        // For now, return empty array or throw exception
        throw new \Exception('Excel parsing not implemented. Please convert to CSV first.');
    }

    /**
     * Parse date string to Y-m-d format
     */
    private function parseDate(string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }
        
        try {
            // Try common formats
            $formats = ['d-M-y', 'd-M-Y', 'd/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y'];
            
            foreach ($formats as $format) {
                try {
                    $date = Carbon::createFromFormat($format, trim($dateString));
                    if ($date) {
                        return $date->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            // Fallback to Carbon's flexible parser
            return Carbon::parse(trim($dateString))->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("Failed to parse date: {$dateString}");
            return null;
        }
    }

    /**
     * Parse amount string to float
     */
    private function parseAmount(string $amountString): float
    {
        if (empty($amountString)) {
            return 0.0;
        }
        
        // Remove spaces, currency symbols, and commas
        $cleaned = preg_replace('/[^\d.-]/', '', str_replace([' ', ','], '', $amountString));
        
        return (float) $cleaned;
    }

    /**
     * Check if string is a valid date
     */
    private function isValidDate(string $dateString): bool
    {
        if (empty($dateString)) {
            return false;
        }
        
        // Check if it matches common date patterns
        $patterns = [
            '/^\d{1,2}-[A-Za-z]{3}-\d{2,4}$/',  // 30-Jun-26
            '/^\d{1,2}\/\d{1,2}\/\d{2,4}$/',    // 30/06/2026
            '/^\d{4}-\d{1,2}-\d{1,2}$/',        // 2026-06-30
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, trim($dateString))) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get supported bank types
     */
    public static function getSupportedBanks(): array
    {
        return [
            'NMB' => 'NMB Bank',
            'FCB' => 'First Capital Bank',
            'FirstCapital' => 'First Capital Bank',
            'Generic' => 'Generic CSV Format',
        ];
    }
}
