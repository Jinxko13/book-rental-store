<?php

namespace App\Console\Commands;

use App\Mail\RentalOverdueMail;
use App\Mail\RentalReminderMail;
use App\Models\Rental;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendRentalNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rental:send-notifications 
                            {--type=all : Type of notification (reminder|overdue|all)}
                            {--days=3 : Days before due date for reminder}
                            {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *      
     * @var string
     */
    protected $description = 'Send rental notifications (reminders and overdue notices)';
    public Rental $rental;
    public function __construct(Rental $rental){
        parent::__construct();
        $this->rental = $rental;
    }
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $reminderDays = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("🚀 Starting rental notifications...");
        $this->info("Type: {$type} | Reminder days: {$reminderDays} | Dry run: " . ($dryRun ? 'Yes' : 'No'));
        $this->newLine();

        $totalSent = 0;

        if ($type === 'reminder' || $type === 'all') {
            $totalSent += $this->sendReminders($reminderDays, $dryRun ? true : false);
        }

        if ($type === 'overdue' || $type === 'all') {
            $totalSent += $this->sendOverdueNotices($dryRun ? true : false);
        }

        $this->newLine();
        $this->info("✅ Completed! Total notifications " . ($dryRun ? 'would be sent' : 'sent') . ": {$totalSent}");

        return Command::SUCCESS;
    }

    /**
     * Send reminder notifications
     */
    private function sendReminders(int $days, bool $dryRun): int
    {
        $this->info("📝 Processing reminder notifications...");

        $rentals = Rental::where('status', 'renting')
            ->whereDate('due_date', '=', today()->addDays($days)->toDateString())
            ->get();

        if ($rentals->isEmpty()) {
            $this->warn("No rentals found for reminder (due in {$days} days)");
            return 0;
        }

        if ($dryRun) {
            $this->info("DRY RUN: Would send {$rentals->count()} reminder emails");
            return $rentals->count();
        }

        $sent = 0;
        $errors = 0;

        foreach ($rentals as $rental) {
            try {
                Mail::to($rental->user->email)
                    ->queue(new RentalReminderMail($rental, $days));

                // $rental->update(['reminded_at' => now()]);

                $this->line("✓ Sent reminder to {$rental->user->email}");
                $sent++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$rental->user->email}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->info("Reminders: {$sent} sent, {$errors} errors");
        return $sent;
    }

    /**
     * Send overdue notifications
     */
    private function sendOverdueNotices(bool $dryRun): int
    {
        $this->info("⚠️  Processing overdue notifications...");

        $rentals = Rental::where('status', 'overdue')
            ->where('due_date', '<', today())
            ->get();

        if ($rentals->isEmpty()) {
            $this->warn("No overdue rentals found");
            return 0;
        }

        // $this->table(
        //     ['User', 'Book', 'Due Date', 'Days Overdue', 'Fine'],
        //     $rentals->map(function($rental) {
        //         $daysOverdue = now()->diffInDays($rental->due_date);
        //         $fine = $this->calculateFine($daysOverdue);
        //         return [
        //             $rental->user->name,
        //             $rental->book->title,
        //             $rental->due_date->format('d/m/Y'),
        //             $daysOverdue,
        //             number_format($fine) . 'đ'
        //         ];
        //     })
        // );

        if ($dryRun) {
            $this->info("DRY RUN: Would send {$rentals->count()} overdue emails");
            return $rentals->count();
        }

        $sent = 0;
        $errors = 0;

        foreach ($rentals as $rental) {
            try {
                Mail::to($rental->user->email)
                    ->queue(new RentalOverdueMail($rental));


                $this->line("✓ Sent overdue notice to {$rental->user->email}");
                $sent++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$rental->user->email}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->info("Overdue notices: {$sent} sent, {$errors} errors");
        return $sent;
    }
}

// Command để gửi email nhắc nhở hàng loạt
class SendBulkRentalReminders extends Command
{
    protected $signature = 'rental:bulk-reminders 
                            {--schedule=daily : Schedule type (daily|weekly|custom)}
                            {--reminder-days=1,3,7 : Days before due date to send reminders}
                            {--dry-run : Preview without sending}';

    protected $description = 'Send bulk rental reminders based on schedule';

    public function handle()
    {
        $schedule = $this->option('schedule');
        $reminderDays = explode(',', $this->option('reminder-days'));
        $dryRun = $this->option('dry-run');

        $this->info("📧 Bulk Rental Reminders");
        $this->info("Schedule: {$schedule}");
        $this->info("Reminder days: " . implode(', ', $reminderDays));
        $this->newLine();

        $totalSent = 0;

        foreach ($reminderDays as $days) {
            $days = (int) trim($days);
            $this->info("Processing {$days}-day reminders...");
            
            $sent = $this->sendRemindersForDays($days, $dryRun);
            $totalSent += $sent;
            
            $this->line("Sent {$sent} reminders for {$days}-day notice");
            $this->newLine();
        }

        $this->info("✅ Total reminders sent: {$totalSent}");
        return Command::SUCCESS;
    }

    private function sendRemindersForDays(int $days, bool $dryRun): int
    {
        $targetDate = now()->addDays($days)->toDateString();
        
        $rentals = Rental::with(['user', 'book'])
            ->where('status', 'active')
            ->whereDate('due_date', $targetDate)
            ->where(function($query) use ($days) {
                $query->whereNull('last_reminder_sent')
                      ->orWhere('last_reminder_days', '!=', $days);
            })
            ->get();

        if ($rentals->isEmpty()) {
            return 0;
        }

        if ($dryRun) {
            $this->table(
                ['User', 'Email', 'Book', 'Due Date'],
                $rentals->map(fn($rental) => [
                    $rental->user->name,
                    $rental->user->email,
                    $rental->book->title,
                    $rental->due_date->format('d/m/Y')
                ])
            );
            return $rentals->count();
        }

        $sent = 0;
        foreach ($rentals as $rental) {
            try {
                Mail::to($rental->user->email)
                    ->send(new RentalReminderMail($rental, $days));

                $rental->update([
                    'last_reminder_sent' => now(),
                    'last_reminder_days' => $days
                ]);

                $sent++;
            } catch (\Exception $e) {
                $this->error("Failed to send reminder to {$rental->user->email}: {$e->getMessage()}");
            }
        }

        return $sent;
    }
}
