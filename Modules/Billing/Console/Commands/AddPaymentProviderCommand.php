<?php

namespace Modules\Billing\Console\Commands;

use Illuminate\Console\Command;
use Modules\Billing\Models\PaymentProvider;

class AddPaymentProviderCommand extends Command
{
    protected $signature = 'payment:add-payment-provider';

    protected $description = 'Add a new payment provider to the database';

    public function handle(): void
    {
        $name = $this->ask('Enter the name of the payment provider');
        $publicKey = $this->ask('Enter the public key');
        $secretKey = $this->ask('Enter the secret key');
        $status = $this->choice('Select the status', ['active', 'inactive'], 0);

        // Convert status to integer
        $status = $status === 'active' ? 1 : 0;

        $paymentProvider = new PaymentProvider([
            'name' => $name,
            'public_key' => $publicKey,
            'secret_key' => $secretKey,
            'status' => $status,
        ]);

        $paymentProvider->save();

        $this->info('Payment provider added successfully!');
    }
}
