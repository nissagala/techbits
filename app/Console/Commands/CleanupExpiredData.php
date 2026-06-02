<?php

namespace App\Console\Commands;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupExpiredData extends Command
{
    protected $signature   = 'app:cleanup-expired-data';
    protected $description = 'Delete expired Unverified accounts, OTPs, and password reset tokens.';

    public function handle(): void
    {
        $deleted = User::where('status', 'unverified')
            ->where('created_at', '<', now()->subHours(24))
            ->delete();
        $this->info("Deleted {$deleted} expired unverified accounts.");

        $otps = Otp::where('expires_at', '<', now())->orWhereNotNull('invalidated_at')->delete();
        $this->info("Pruned {$otps} expired/invalidated OTPs.");

        $tokens = DB::table('password_reset_tokens')
            ->where('created_at', '<', now()->subHour())
            ->delete();
        $this->info("Pruned {$tokens} expired password reset tokens.");
    }
}
