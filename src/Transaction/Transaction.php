<?php


namespace PiSdk\Api\Transaction;

use phpseclib3\Math\BigInteger;
use PiSdk\Api\Model\PiAmount;
use PiSdk\Api\Server;
use PiSdk\Api\Xdr\XdrBuffer;
use PiSdk\Api\XdrModel\AccountId;
use PiSdk\Api\XdrModel\Memo;
use PiSdk\Api\XdrModel\Operation\Operation;
use PiSdk\Api\XdrModel\TimeBounds;


/**
 * NOTE: currently this class is only used for parsing from XDR and is not returned
 * by the TransactionBuilder
 */
class Transaction
{
    /**
     * @var AccountId
     */
    protected $sourceAccountId;

    /**
     * @var TimeBounds
     */
    protected $timeBounds;

    /**
     * @var Memo
     */
    protected $memo;

    /**
     * @var Operation[]
     */
    protected $operations = array();

    /**
     * @var BigInteger
     */
    protected $sequenceNumber;

    /**
     * @var PiAmount
     */
    protected $feePaid;

    /**
     * @param XdrBuffer $xdr
     * @return Transaction
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $tx = new Transaction();

        $tx->sourceAccountId = AccountId::fromXdr($xdr);
        $tx->feePaid = new PiAmount($xdr->readUnsignedInteger());
        $tx->sequenceNumber = $xdr->readBigInteger();

        // time bounds are optional
        if ($xdr->readBoolean()) {
            $tx->timeBounds = TimeBounds::fromXdr($xdr);
        }

        $tx->memo = Memo::fromXdr($xdr);

        $numOperations = $xdr->readUnsignedInteger();
        for ($i=0; $i < $numOperations; $i++) {
            $tx->operations[] = Operation::fromXdr($xdr);
        }

        // 4 bytes at the end are reserved for future use
        $xdr->readOpaqueFixed(4);

        return $tx;
    }

    public function __construct()
    {
        $this->timeBounds = new TimeBounds();
    }

    /**
     * @param Server $server
     * @return TransactionBuilder
     */
    public function toTransactionBuilder(Server $server = null)
    {
        $builder = new TransactionBuilder($this->sourceAccountId->getAccountIdString());

        if ($server) {
            $builder->setApiClient($server->getApiClient());
        }


        $builder->setSequenceNumber($this->sequenceNumber);

        if (!$this->timeBounds->isEmpty()) {
            $builder->setLowerTimebound($this->timeBounds->getMinTime());
            $builder->setUpperTimebound($this->timeBounds->getMaxTime());
        }

        $builder->setMemo($this->memo);

        foreach ($this->operations as $operation) {
            $builder->addOperation($operation);
        }

        return $builder;
    }

    /**
     * @return AccountId
     */
    public function getSourceAccountId()
    {
        return $this->sourceAccountId;
    }

    /**
     * @return TimeBounds
     */
    public function getTimeBounds()
    {
        return $this->timeBounds;
    }

    /**
     * @return Memo
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * @return Operation[]
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * @return BigInteger
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }

    /**
     * @return PiAmount
     */
    public function getFeePaid()
    {
        return $this->feePaid;
    }
}
