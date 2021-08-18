<?php


namespace PiSdk\Api\Signing;

use PiSdk\Api\Transaction\TransactionBuilder;
use PiSdk\Api\XdrModel\DecoratedSignature;

interface SigningInterface
{
    /**
     * Returns a DecoratedSignature for the given TransactionBuilder
     *
     * @param TransactionBuilder $builder
     * @return DecoratedSignature
     */
    public function signTransaction(TransactionBuilder $builder);
}