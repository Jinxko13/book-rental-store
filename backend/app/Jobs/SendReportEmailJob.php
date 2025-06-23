<?php

namespace App\Jobs;

use App\Mail\SendReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendReportEmailJob implements ShouldQueue
{
    use Queueable;

   protected $email;
   protected $filename;
   protected $data;
    public function __construct($email, $filename,$data)
    {
        $this->email = $email;
        $this->filename = $filename;
        $this->data = $data;
    }

    public function handle(): void
    {
        Mail::to($this->email)->send(new SendReport($this->filename,$this->data));
    }
}
