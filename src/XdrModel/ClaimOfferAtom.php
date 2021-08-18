<?php


namespace PiSdk\Api\XdrModel;


use phpseclib3\Math\BigInteger;
use PiSdk\Api\Model\PiAmount;
use PiSdk\Api\Xdr\XdrBuffer;

/**
 * Used when returning information about operations that claimed offers
 */
class ClaimOfferAtom
{
    /**
     * @var AccountId
     */
    protected $seller;

    /**
     * @var int (64-bit)
     */
    protected $offerId;

    /**
     * @var Asset
     */
    protected $assetSold;

    /**
     * @var PiAmount
     */
    protected $amountSold;

    /**
     * @var Asset
     */
    protected $assetBought;

    /**
     * @var PiAmount
     */
    protected $amountBought;

    /**
     * @param XdrBuffer $xdr
     * @return ClaimOfferAtom
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new ClaimOfferAtom();

        $model->seller = AccountId::fromXdr($xdr);
        $model->offerId = $xdr->readUnsignedInteger64();

        $model->assetSold = Asset::fromXdr($xdr);
        $model->amountSold = new PiAmount(new BigInteger($xdr->readInteger64()));

        $model->assetBought = Asset::fromXdr($xdr);
        $model->amountBought = new PiAmount(new BigInteger($xdr->readInteger64()));

        return $model;
    }

    /**
     * @return AccountId
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * @return int
     */
    public function getOfferId()
    {
        return $this->offerId;
    }

    /**
     * @return Asset
     */
    public function getAssetSold()
    {
        return $this->assetSold;
    }

    /**
     * @return PiAmount
     */
    public function getAmountSold()
    {
        return $this->amountSold;
    }

    /**
     * @return Asset
     */
    public function getAssetBought()
    {
        return $this->assetBought;
    }

    /**
     * @return PiAmount
     */
    public function getAmountBought()
    {
        return $this->amountBought;
    }
}
