<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogTaskAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $action;
    protected int $taskId;

    public function __construct(string $action, int $taskId)
    {
        $this->action = $action;
        $this->taskId = $taskId;
    }

    public function handle(): void
    {
        Log::info("Task action logged: {$this->action} for Task ID: {$this->taskId}");
    }
}
