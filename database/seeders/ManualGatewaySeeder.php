<?php

namespace Database\Seeders;

use App\Models\Gateway;
use App\Models\GatewayCurrency;
use App\Models\Form;
use Illuminate\Database\Seeder;
use App\Constants\Status;

class ManualGatewaySeeder extends Seeder
{
    public function run()
    {
        // 1. Bank Transfer
        $bankForm = Form::updateOrCreate(
            ['act' => 'manual_deposit_bank'],
            [
                'form_data' => [
                    'account_name' => [
                        'name' => 'Account Name',
                        'label' => 'account_name',
                        'is_required' => 'required',
                        'instruction' => 'Enter the name of the bank account you paid from',
                        'extensions' => null,
                        'options' => [],
                        'type' => 'text',
                        'width' => '12'
                    ],
                    'deposit_slip' => [
                        'name' => 'Deposit Slip / Screenshot',
                        'label' => 'deposit_slip',
                        'is_required' => 'required',
                        'instruction' => 'Upload a clear copy of your payment confirmation',
                        'extensions' => 'jpg,jpeg,png,pdf',
                        'options' => [],
                        'type' => 'file',
                        'width' => '12'
                    ],
                    'transaction_reference' => [
                        'name' => 'Transaction Reference',
                        'label' => 'transaction_reference',
                        'is_required' => 'optional',
                        'instruction' => 'Enter any reference number provided by the bank',
                        'extensions' => null,
                        'options' => [],
                        'type' => 'text',
                        'width' => '12'
                    ]
                ]
            ]
        );

        $bankGateway = Gateway::updateOrCreate(
            ['code' => 1000],
            [
                'form_id' => $bankForm->id,
                'name' => 'Bank Transfer',
                'alias' => 'bank_transfer',
                'status' => Status::ENABLE,
                'crypto' => 0,
                'description' => '<p>Please make your payment to the following bank account:</p><ul><li><strong>Bank Name:</strong> Global Investment Bank</li><li><strong>Account Name:</strong> Your Net Investment Ltd</li><li><strong>Account Number:</strong> 1234567890</li><li><strong>Sort Code:</strong> 12-34-56</li><li><strong>IBAN:</strong> GB12 GIBL 1234 5678 90</li><li><strong>SWIFT/BIC:</strong> GIBLGB2L</li></ul><p><strong>Note:</strong> Please include your username as a reference in the payment to expedite verification.</p>'
            ]
        );

        GatewayCurrency::updateOrCreate(
            ['method_code' => 1000],
            [
                'name' => 'Bank Transfer',
                'currency' => 'USD',
                'symbol' => '$',
                'gateway_alias' => 'bank_transfer',
                'min_amount' => 100,
                'max_amount' => 500000,
                'percent_charge' => 0,
                'fixed_charge' => 0,
                'rate' => 1,
            ]
        );

        // 2. Bitcoin (BTC)
        $btcForm = Form::updateOrCreate(
            ['act' => 'manual_deposit_btc'],
            [
                'form_data' => [
                    'transaction_hash' => [
                        'name' => 'Transaction ID / Hash',
                        'label' => 'transaction_hash',
                        'is_required' => 'required',
                        'instruction' => 'Paste the BTC transaction hash here',
                        'extensions' => null,
                        'options' => [],
                        'type' => 'text',
                        'width' => '12'
                    ],
                    'screenshot' => [
                        'name' => 'Payment Screenshot',
                        'label' => 'screenshot',
                        'is_required' => 'required',
                        'instruction' => 'Upload a screenshot from your wallet showing the transfer',
                        'extensions' => 'jpg,jpeg,png',
                        'options' => [],
                        'type' => 'file',
                        'width' => '12'
                    ]
                ]
            ]
        );

        $btcGateway = Gateway::updateOrCreate(
            ['code' => 1001],
            [
                'form_id' => $btcForm->id,
                'name' => 'Bitcoin (BTC)',
                'alias' => 'bitcoin_manual',
                'status' => Status::ENABLE,
                'crypto' => 1,
                'description' => '<p>Please send the exact BTC amount to the address below:</p><div class="alert alert-info"><strong>BTC Address:</strong> {addr}</div><p><strong>Instructions:</strong></p><ol><li>Open your crypto wallet (e.g., Trust Wallet, Binance).</li><li>Scan the QR code or copy the address above.</li><li>Send the designated amount of Bitcoin (BTC).</li><li>Once the transaction is confirmed on the blockchain (usually 1-3 confirmations), provide the details below.</li></ol><p class="text-danger"><strong>Disclaimer:</strong> Send only BTC to this address. Sending any other coin (including BCH or BSV) may result in permanent loss of funds.</p>'
            ]
        );

        GatewayCurrency::updateOrCreate(
            ['method_code' => 1001],
            [
                'name' => 'Bitcoin',
                'currency' => 'BTC',
                'symbol' => '₿',
                'gateway_alias' => 'bitcoin_manual',
                'min_amount' => 0.0001,
                'max_amount' => 10,
                'percent_charge' => 0,
                'fixed_charge' => 0,
                'rate' => 65000, 
            ]
        );

        // 3. Ethereum (ETH)
        $ethForm = Form::updateOrCreate(
            ['act' => 'manual_deposit_eth'],
            [
                'form_data' => [
                    'transaction_hash' => [
                        'name' => 'Transaction Hash',
                        'label' => 'transaction_hash',
                        'is_required' => 'required',
                        'instruction' => 'Paste the ETH transaction hash',
                        'extensions' => null,
                        'options' => [],
                        'type' => 'text',
                        'width' => '12'
                    ],
                    'screenshot' => [
                        'name' => 'Payment Screenshot',
                        'label' => 'screenshot',
                        'is_required' => 'required',
                        'instruction' => 'Upload a screenshot showing the transfer details',
                        'extensions' => 'jpg,jpeg,png',
                        'options' => [],
                        'type' => 'file',
                        'width' => '12'
                    ]
                ]
            ]
        );

        $ethGateway = Gateway::updateOrCreate(
            ['code' => 1002],
            [
                'form_id' => $ethForm->id,
                'name' => 'Ethereum (ETH)',
                'alias' => 'ethereum_manual',
                'status' => Status::ENABLE,
                'crypto' => 1,
                'description' => '<p>Please send the exact ETH amount to the address below:</p><div class="alert alert-info"><strong>ETH Address:</strong> {addr}</div><p><strong>Instructions:</strong></p><ol><li>Open your Ethereum wallet (e.g., MetaMask, Ledger).</li><li>Ensure you are connected to the <strong>Ethereum Mainnet (ERC-20)</strong>.</li><li>Send the exact amount to the address above.</li></ol><p class="text-danger"><strong>Disclaimer:</strong> Send only ETH to this address. multi-chain transfers (like BSC, Polygon, or Arbitrum) are NOT supported and will lead to loss of funds.</p>'
            ]
        );

        GatewayCurrency::updateOrCreate(
            ['method_code' => 1002],
            [
                'name' => 'Ethereum',
                'currency' => 'ETH',
                'symbol' => 'Ξ',
                'gateway_alias' => 'ethereum_manual',
                'min_amount' => 0.01,
                'max_amount' => 100,
                'percent_charge' => 0,
                'fixed_charge' => 0,
                'rate' => 3500, // Placeholder rate
            ]
        );
    }
}
