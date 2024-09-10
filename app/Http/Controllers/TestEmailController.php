// app/Http/Controllers/TestEmailController.php

namespace App\Http\Controllers;

use App\Mail\TestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestEmailController extends Controller
{
    public function send()
    {
        Mail::to('your_email@example.com')->send(new TestEmail());
        return 'Test email sent!';
    }
}
