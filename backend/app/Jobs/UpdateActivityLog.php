<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\ActivityLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateActivityLog implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $action,
        public string $modelType,
        public int $modelId,
        public array $payload,
        public ?int $userId,
        public ?string $ipAddress,
        public ?string $computerName
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            \Log::info('[UpdateActivityLog] Running job', ['action' => $this->action]);
    
            ActivityLog::create([
                'action'        => $this->action,
                'model_type'    => $this->modelType,
                'model_id'      => $this->modelId,
                'payload'       => json_encode($this->payload, JSON_UNESCAPED_UNICODE),
                'user_id'       => $this->userId,
                'ip_address'    => $this->ipAddress,
                'computer_name' => $this->computerName,
                'date_log'      => now(),
                'active'        => true,
            ]);
        } catch (\Throwable $e) {
            \Log::error('[UpdateActivityLog] Failed', ['message' => $e->getMessage()]);
        }
    }
}
