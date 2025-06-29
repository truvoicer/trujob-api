<?php

namespace App\Console\Commands;

use App\Mail\TestMail as MailTestMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set default pages and menus';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending test email...');
        // Here you would trigger the email sending logic
        // For example, you might dispatch a job to send the email
        Mail::to('recipient@example.com')->send(new MailTestMail());
    }
}
