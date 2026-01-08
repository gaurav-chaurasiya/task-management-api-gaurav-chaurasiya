<?php

namespace App\Listeners;

use App\Events\TaskUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifyUserOfTaskUpdate implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TaskUpdated $event): void
    {
        // Simulated notification logic
        Log::info("User notified of task update: " . $event->task->title);
    }
}
