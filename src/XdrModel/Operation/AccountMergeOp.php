<?php


namespace Greenarmor\\PiSdk\XdrModel\Operation;


use Greenarmor\\PiSdk\Keypair;
use Greenarmor\\PiSdk\Xdr\XdrBuffer;
use Greenarmor\\PiSdk\XdrModel\AccountId;

class AccountMergeOp extends Operation
{
    /**
     * @var AccountId
     */
    protected $destination;

    public function __construct($destinationAccountId, $sourceAccountId = null)
    {
        parent::__construct(Operation::TYPE_ACCOUNT_MERGE, $sourceAccountId);

        if ($destinationAccountId instanceof Keypair) {
            $destinationAccountId = $destinationAccountId->getPublicKey();
        }

        $this->destination = new AccountId($destinationAccountId);
    }

    /**
     * @return string
     */
    public function toXdr()
    {
        $bytes = parent::toXdr();

        // Destination account
        $bytes .= $this->destination->toXdr();

        return $bytes;
    }

    /**
     * NOTE: This only parses the XDR that's specific to this operation and cannot
     * load a full Operation
     *
     * @deprecated Do not call this directly, instead call Operation::fromXdr()
     * @param XdrBuffer $xdr
     * @return AccountMergeOp
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $destination = AccountId::fromXdr($xdr);

        return new AccountMergeOp($destination->getAccountIdString());
    }

    /**
     * @return AccountId
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param AccountId $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }
}