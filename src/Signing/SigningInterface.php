<?php


namespace Greenarmor\\PiSdk\Signing;

use Greenarmor\\PiSdk\Transaction\TransactionBuilder;
use Greenarmor\\PiSdk\XdrModel\DecoratedSignature;

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