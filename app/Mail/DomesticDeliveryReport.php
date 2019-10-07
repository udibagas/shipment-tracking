<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DomesticDeliveryReport extends Mailable
{
    use Queueable, SerializesModels;

    protected $report;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($report)
    {
        $this->report = $report;
        $this->subject($report->subject);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.domestic_delivery_report')
            ->with(['data' => $this->report]);
    }
}
