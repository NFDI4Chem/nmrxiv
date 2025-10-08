<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckCspViolations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:check-csp-violations {--days=7 : Number of days to check back} {--show-details : Show detailed violation information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check recent CSP violations and provide recommendations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $showDetails = $this->option('show-details');

        $this->info("Checking CSP violations from the last {$days} day(s)...");

        $logPath = storage_path('logs/laravel.log');

        if (! file_exists($logPath)) {
            $this->warn('No log file found. CSP violations would appear in: '.$logPath);

            return self::SUCCESS;
        }

        $result = $this->analyzeLogFile($logPath, $days, $showDetails);
        $violationCount = $result['count'];

        if ($violationCount === 0) {
            $this->info('✅ No CSP violations found! Your Content Security Policy appears to be working correctly.');
        } else {
            $this->warn("⚠️  Found {$violationCount} CSP violation(s)");
            $this->newLine();
            $this->line('💡 Recommendations:');
            $this->line('1. Review the blocked URIs above');
            $this->line('2. Add legitimate sources to your CSP policy');
            $this->line('3. Remove or update sources of violations');
            $this->line('4. Consider tightening your policy if violations are from malicious sources');
        }

        return self::SUCCESS;
    }

    private function analyzeLogFile(string $logPath, int $days, bool $showDetails): array
    {
        $cutoffDate = now()->subDays($days);
        $violationCount = 0;
        $violationSummary = [];
        $violationDetails = [];

        $handle = fopen($logPath, 'r');
        if (! $handle) {
            $this->error('Could not read log file');

            return ['count' => 0, 'summary' => [], 'details' => []];
        }

        while (($line = fgets($handle)) !== false) {
            if (strpos($line, 'CSP Violation Detected') !== false) {
                // Extract timestamp from log line
                if (preg_match('/\[([\d\-\s:]+)\]/', $line, $matches)) {
                    $logDate = \Carbon\Carbon::parse($matches[1]);

                    if ($logDate->gte($cutoffDate)) {
                        $violationCount++;
                        $violationDetails[] = [
                            'timestamp' => $logDate->format('Y-m-d H:i:s'),
                            'line' => trim($line),
                        ];

                        if ($showDetails) {
                            $this->line('🚫 '.trim($line));
                        } else {
                            // Extract blocked URI for summary
                            if (preg_match('/"blocked_uri":"([^"]+)"/', $line, $uriMatches)) {
                                $uri = $uriMatches[1];
                                $violationSummary[$uri] = ($violationSummary[$uri] ?? 0) + 1;
                            }
                        }
                    }
                }
            }
        }

        fclose($handle);

        if (! $showDetails && ! empty($violationSummary)) {
            $this->line('📊 Violation Summary:');
            foreach ($violationSummary as $uri => $count) {
                $this->line("   {$count}x {$uri}");
            }
        }

        return [
            'count' => $violationCount,
            'summary' => $violationSummary,
            'details' => $violationDetails,
        ];
    }
}
