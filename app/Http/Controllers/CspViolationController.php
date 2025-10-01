<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CspViolationController extends Controller
{
    public function index()
    {
        // Parse violations from log files
        $violations = $this->parseViolationsFromLogs();
        
        return response()->json([
            'violations' => $violations,
            'total' => count($violations),
            'message' => 'CSP Violations retrieved from logs'
        ]);
    }
    
    private function parseViolationsFromLogs($limit = 50)
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!file_exists($logPath)) {
            return [];
        }
        
        $logContent = file_get_contents($logPath);
        // Match CSP violation log entries - the JSON is all on one line
        preg_match_all('/\[(.*?)\].*?CSP Violation Detected\s+(\{.+?\})(?=\s*\n|\s*$)/m', $logContent, $matches, PREG_SET_ORDER);
        
        $violations = [];
        foreach (array_reverse($matches) as $match) {
            if (count($violations) >= $limit) break;
            
            try {
                $data = json_decode($match[2], true);
                $violations[] = [
                    'timestamp' => $match[1],
                    'document_uri' => $data['document_uri'] ?? 'unknown',
                    'blocked_uri' => $data['blocked_uri'] ?? 'unknown',
                    'violated_directive' => $data['violated_directive'] ?? 'unknown',
                    'source_file' => $data['source_file'] ?? 'unknown',
                    'line_number' => $data['line_number'] ?? 'unknown',
                    'ip_address' => $data['ip_address'] ?? 'unknown',
                ];
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return $violations;
    }

    public function report(Request $request)
    {
        $violation = $request->input('csp-report');
        
        if ($violation) {
            Log::channel('stack')->warning('CSP Violation Detected', [
                'document_uri' => $violation['document-uri'] ?? 'unknown',
                'blocked_uri' => $violation['blocked-uri'] ?? 'unknown',
                'violated_directive' => $violation['violated-directive'] ?? 'unknown',
                'source_file' => $violation['source-file'] ?? 'unknown',
                'line_number' => $violation['line-number'] ?? 'unknown',
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
                'ip_address' => $request->ip(),
                'full_report' => $violation,
            ]);
        }
        
        return response('', 204);
    }
}
