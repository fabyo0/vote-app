<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Idea;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IdeaStatusUpdatedMailable extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $idea;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
    }

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
