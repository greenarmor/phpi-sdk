<?php


namespace Greenarmor\\PiSdk\XdrModel;


use Greenarmor\\PiSdk\Model\PiAmount;
use Greenarmor\\PiSdk\Xdr\XdrBuffer;

class InflationPayout
{
    /**
     * @var AccountId
     */
    protected $destination;

    /**
     * @var PiAmount
     */
    protected $amount;

    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new InflationPayout();

        $model->destination = AccountId::fromXdr($xdr);
        $model->amount = new PiAmount($xdr->readInteger64());

        return $model;
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

    /**
     * @return PiAmount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param PiAmount $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
}