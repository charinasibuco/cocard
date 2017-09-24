<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\QueuedMessageController;
use Acme\Repositories\QueuedMessageRepository;
class ReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(QueuedMessageRepository $queued_message)
    {
        parent::__construct();
        $this->queued_message = $queued_message;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $x = new QueuedMessageController(); 
        return $x->checkDateToSendReminder($this->queued_message);
    }
}
