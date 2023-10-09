<?php

namespace App\Mail;

use App\Models\Idea;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Log;

class IdeaStatusUpdatedMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public Idea $idea)
    {}


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->
        subject('An idea you voted for has a new status')
            ->markdown('emails.idea-status.updated');
    }
}
