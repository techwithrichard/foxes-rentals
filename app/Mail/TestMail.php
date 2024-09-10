// app/Mail/TestEmail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        // Constructor can be used for passing dynamic data
    }

    public function build()
    {
        return $this->view('emails.test')
                    ->subject('Test Email from Laravel');
    }
}
