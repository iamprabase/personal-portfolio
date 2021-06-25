<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubscriptionCreated extends Mailable
{
    use Queueable, SerializesModels;
    public $register_link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($register_link)
    {
        $this->register_link = $register_link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Hello there! Subscription Created!')->view('emails.subscription.created');
    }
}
