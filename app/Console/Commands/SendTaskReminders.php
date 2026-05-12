<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Task;
use App\Mail\TaskReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for tasks due in 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        
        $users = User::all();

        foreach ($users as $user) {
            $tasksDue = Task::whereHas('event', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('due_date', $tomorrow)
            ->where('status', '!=', 'done')
            ->get();

            if ($tasksDue->count() > 0) {
                Mail::to($user->email)->send(new TaskReminderMail($tasksDue));
                $this->info("Reminder sent to {$user->email}");
            }
        }
    }
}
