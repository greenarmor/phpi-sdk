<?php


namespace PiSdk\Api\Signing;


use PiSdk\Api\Keypair;
use PiSdk\Api\Transaction\TransactionBuilder;
use PiSdk\Api\XdrModel\DecoratedSignature;

/**
 * Capable of signing with a Keypair containing the secret key
 */
class PrivateKeySigner implements SigningInterface
{
    /**
     * @var Keypair
     */
    protected $keypair;

    public function __construct(Keypair $keypair)
    {
        $this->keypair = $keypair;
    }

    /**
     * @param TransactionBuilder $builder
     * @return DecoratedSignature
     */
    public function signTransaction(TransactionBuilder $builder)
    {
        $hash = $builder
            ->getTransactionEnvelope()
            ->getHash();

        return $this->keypair->signDecorated($hash);
    }
}