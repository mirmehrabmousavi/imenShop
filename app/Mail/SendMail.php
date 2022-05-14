<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $title;
    public $massage;
    public $email;
    public function __construct($title,$massage,$email)
    {
        $this->title = $title;
        $this->massage = $massage;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $messages = $this->massage;
        $titles = $this->title;
        return $this->view('email.index' , compact('messages','titles'))->from($this->email)->subject($this->title);
    }
}
