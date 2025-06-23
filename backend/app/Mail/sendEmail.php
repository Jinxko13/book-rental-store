<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $data;
    protected $range;
    public function __construct($data,$range)
    {
        $this->data = $data;
        $this->range = $range;
    }
    public function envelope(): Envelope
    {
        $TitleEmail='Báo cáo Thong ke';
        if($this->range=='daily'){
            $TitleEmail=$TitleEmail." "."theo ngay";
        }
        if($this->range=='month'){
            $TitleEmail=$TitleEmail." "."theo thang";
        }
        if($this->range=='week'){
            $TitleEmail=$TitleEmail." "."theo tuan";
        }
        return new Envelope(subject: $TitleEmail);
    }


    public function content(): Content
    {
        $datas=(object) $this->data;
        return new Content(
            view: 'emails.report',
            with: [
                "data" => $datas,
            ]
        );
    }

    public function attachments(): array
    {
        return [

        ];
    }
}
