<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\UserSubscription;
use Illuminate\Support\Facades\Log;

class CheckUserSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-subscriptions:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and handle user subscriptions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userSubscriptionsExpireToday = UserSubscription::whereDate('next_payment_at', Carbon::today()->toDateString())->get();
        foreach ($userSubscriptionsExpireToday as $subscription) {
            $subscription->update(['status' => 'inactive']);
        }
    }
}
