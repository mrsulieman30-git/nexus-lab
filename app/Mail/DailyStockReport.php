<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyStockReport extends Mailable
{
    use Queueable, SerializesModels;

    public $lowStock;
    public $expiring;
    public $labName;

    /**
     * Create a new message instance.
     */
    public function __construct($lowStock, $expiring, $labName)
    {
        $this->lowStock = $lowStock;
        $this->expiring = $expiring;
        $this->labName = $labName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Action Required: Inventory Alert - ' . $this->labName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_stock_report',
        );
    }
}
