<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendReport extends Mailable
{
    use Queueable, SerializesModels;
    protected $fileName;
    protected $data;


    public function __construct($fileName,$data)
    {
        $this->fileName = $fileName;
        $this->data = $data;
    }


    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Báo cáo Thong ke');
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
            Attachment::fromPath(public_path('excel/' . $this->fileName))
                ->as('BaoCao.xlsx')
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}
