<?php


namespace Greenarmor\\PiSdk\XdrModel;


use Greenarmor\\PiSdk\Keypair;
use Greenarmor\\PiSdk\Server;
use Greenarmor\\PiSdk\Transaction\Transaction;
use Greenarmor\\PiSdk\Transaction\TransactionBuilder;
use Greenarmor\\PiSdk\Util\Debug;
use Greenarmor\\PiSdk\Util\Hash;
use Greenarmor\\PiSdk\Xdr\Iface\XdrEncodableInterface;
use Greenarmor\\PiSdk\Xdr\Type\VariableArray;
use Greenarmor\\PiSdk\Xdr\XdrBuffer;
use Greenarmor\\PiSdk\Xdr\XdrEncoder;

class TransactionEnvelope implements XdrEncodableInterface
{
    const TYPE_SCP  = 1;
    const TYPE_TX   = 2;
    const TYPE_AUTH = 3;

    /**
     * @var TransactionBuilder[]
     */
    private $transactionBuilder;

    /**
     * @var VariableArray of DecoratedSignature
     */
    private $signatures;

    public function __construct(TransactionBuilder $transactionBuilder)
    {
        $this->transactionBuilder = $transactionBuilder;
        $this->signatures = new VariableArray();

        return $this;
    }

    public function toXdr()
    {
        $bytes = '';

        $bytes .= $this->transactionBuilder->toXdr();
        $bytes .= $this->signatures->toXdr();

        return $bytes;
    }

    /**
     * @param XdrBuffer $xdr
     * @return TransactionEnvelope
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $builder = Transaction::fromXdr($xdr)->toTransactionBuilder();

        $model = new TransactionEnvelope($builder);

        $numSignatures = $xdr->readUnsignedInteger();
        for ($i=0; $i < $numSignatures; $i++) {
            $model->signatures->append(DecoratedSignature::fromXdr($xdr));
        }

        return $model;
    }

    /**
     * @return string
     */
    public function toBase64()
    {
        return base64_encode($this->toXdr());
    }

    /**
     * Returns the hash of the transaction envelope
     *
     * This hash is what is signed
     *
     * @return string
     */
    public function getHash()
    {
        return $this->transactionBuilder->hash();
    }

    /**
     * Adds signatures using the given keypairs or secret key strings
     *
     * @param Keypair[]|string[] $keypairsOrsecretKeyStrings
     * @return $this
     */
    public function sign($keypairsOrsecretKeyStrings, Server $server = null)
    {
        if (!is_array($keypairsOrsecretKeyStrings)) $keypairsOrsecretKeyStrings = [$keypairsOrsecretKeyStrings];

        $transactionHash = null;
        if ($server) {
            $transactionHash = $server->getApiClient()->hash($this->transactionBuilder);
        }
        else {
            $transactionHash = $this->transactionBuilder->hash();
        }

        foreach ($keypairsOrsecretKeyStrings as $keypairOrSecretKeyString) {
            if (is_string($keypairOrSecretKeyString)) {
                $keypairOrSecretKeyString = Keypair::newFromSeed($keypairOrSecretKeyString);
            }

            $decorated = $keypairOrSecretKeyString->signDecorated($transactionHash);
            $this->signatures->append($decorated);
        }

        return $this;
    }


    public function addRawSignature($signatureBytes, Keypair $keypair)
    {
        $decorated = new DecoratedSignature($keypair->getHint(), $signatureBytes);

        $this->signatures->append($decorated);
    }

    /**
     * @param DecoratedSignature $decoratedSignature
     */
    public function addDecoratedSignature(DecoratedSignature $decoratedSignature)
    {
        $this->signatures->append($decoratedSignature);
    }

    /**
     * @return DecoratedSignature[]
     */
    public function getDecoratedSignatures()
    {
        return $this->signatures->toArray();
    }
}