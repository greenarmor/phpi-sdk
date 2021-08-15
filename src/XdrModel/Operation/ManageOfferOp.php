<?php


namespace Greenarmor\\PiSdk\XdrModel\Operation;


use phpseclib3\Math\BigInteger;
use Greenarmor\\PiSdk\Model\AssetAmount;
use Greenarmor\\PiSdk\Model\PiAmount;
use Greenarmor\\PiSdk\Util\Debug;
use Greenarmor\\PiSdk\Xdr\XdrBuffer;
use Greenarmor\\PiSdk\Xdr\XdrEncoder;
use Greenarmor\\PiSdk\XdrModel\AccountId;
use Greenarmor\\PiSdk\XdrModel\Asset;
use Greenarmor\\PiSdk\XdrModel\Price;

/**
 * https://github.com/stellar/stellar-core/blob/master/src/xdr/Stellar-transaction.x#L93
 *
 * To update an offer, pass the $offerId
 *
 * To cancel an offer, pass the $offerId and set the $amount to 0
 */
class ManageOfferOp extends Operation
{
    /**
     * @var Asset
     */
    protected $sellingAsset;

    /**
     * @var Asset
     */
    protected $buyingAsset;

    /**
     *
     * @var PiAmount
     */
    protected $amount;

    /**
     * @var Price
     */
    protected $price;

    /**
     * @var int
     */
    protected $offerId;

    /**
     * ManageOfferOp constructor.
     *
     * @param Asset $sellingAsset
     * @param Asset $buyingAsset
     * @param int|BigInteger $amount int representing lumens or BigInteger representing stroops
     * @param Price $price
     * @param null  $offerId
     * @param null  $sourceAccount
     */
    public function __construct(Asset $sellingAsset, Asset $buyingAsset, $amount, Price $price, $offerId = null, $sourceAccount = null)
    {
        parent::__construct(Operation::TYPE_MANAGE_OFFER, $sourceAccount);

        $this->sellingAsset = $sellingAsset;
        $this->buyingAsset = $buyingAsset;
        $this->amount = new PiAmount($amount);
        $this->price = $price;
        $this->offerId = $offerId;
    }

    /**
     * @return string XDR bytes
     */
    public function toXdr()
    {
        $bytes = parent::toXdr();

        $bytes .= $this->sellingAsset->toXdr();
        $bytes .= $this->buyingAsset->toXdr();
        $bytes .= XdrEncoder::signedBigInteger64($this->amount->getUnscaledBigInteger());
        $bytes .= $this->price->toXdr();
        $bytes .= XdrEncoder::unsignedInteger64($this->offerId);

        return $bytes;
    }

    /**
     * @deprecated Do not call this directly, instead call Operation::fromXdr()
     * @param XdrBuffer $xdr
     * @return ManageOfferOp|Operation
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $sellingAsset = Asset::fromXdr($xdr);
        $buyingAsset = Asset::fromXdr($xdr);
        $amount = PiAmount::fromXdr($xdr);
        $price = Price::fromXdr($xdr);
        $offerId = $xdr->readUnsignedInteger64();

        return new ManageOfferOp($sellingAsset,
            $buyingAsset,
            $amount->getUnscaledBigInteger(),
            $price,
            $offerId
        );
    }

    /**
     * @return Asset
     */
    public function getSellingAsset()
    {
        return $this->sellingAsset;
    }

    /**
     * @param Asset $sellingAsset
     */
    public function setSellingAsset($sellingAsset)
    {
        $this->sellingAsset = $sellingAsset;
    }

    /**
     * @return Asset
     */
    public function getBuyingAsset()
    {
        return $this->buyingAsset;
    }

    /**
     * @param Asset $buyingAsset
     */
    public function setBuyingAsset($buyingAsset)
    {
        $this->buyingAsset = $buyingAsset;
    }

    /**
     * @return PiAmount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return Price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param Price $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getOfferId()
    {
        return $this->offerId;
    }

    /**
     * @param int $offerId
     */
    public function setOfferId($offerId)
    {
        $this->offerId = $offerId;
    }
}
