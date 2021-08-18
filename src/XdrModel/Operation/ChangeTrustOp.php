<?php


namespace PiSdk\Api\XdrModel\Operation;

use phpseclib3\Math\BigInteger;
use PiSdk\Api\Model\PiAmount;
use PiSdk\Api\Util\MathSafety;
use PiSdk\Api\Xdr\XdrBuffer;
use PiSdk\Api\Xdr\XdrEncoder;
use PiSdk\Api\XdrModel\AccountId;
use PiSdk\Api\XdrModel\Asset;


/**
 * Manages trust lines
 *
 * https://www.stellar.org/developers/horizon/reference/resources/operation.html#change-trust
 */
class ChangeTrustOp extends Operation
{
    /**
     * @var Asset
     */
    protected $asset;

    /**
     * @var PiAmount
     */
    protected $limit;

    /**
     * ChangeTrustOp constructor.
     *
     * @param Asset          $asset
     * @param int|BigInteger $limit int representing lumens or BigInteger representing stroops
     * @param AccountId|null $sourceAccount
     */
    public function __construct(Asset $asset, $limit, AccountId $sourceAccount = null)
    {
        parent::__construct(Operation::TYPE_CHANGE_TRUST, $sourceAccount);

        $this->asset = $asset;
        $this->setLimit($limit);
    }

    public function toXdr()
    {
        $bytes = parent::toXdr();

        $bytes .= $this->asset->toXdr();
        $bytes .= XdrEncoder::signedBigInteger64($this->limit->getUnscaledBigInteger());

        return $bytes;
    }

    /**
     * @deprecated Do not call this directly, instead call Operation::fromXdr()
     * @param XdrBuffer $xdr
     * @return ChangeTrustOp|Operation
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $asset = Asset::fromXdr($xdr);
        $limit = PiAmount::fromXdr($xdr);

        return new ChangeTrustOp($asset, $limit->getUnscaledBigInteger());
    }

    /**
     * Sets the limit of the trust line to the maximum amount
     */
    public function setMaxLimit()
    {
        $this->limit = PiAmount::newMaximum();
    }

    /**
     * @return PiAmount
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int|BigInteger $limit int representing lumens or BigInteger representing stroops
     */
    public function setLimit($limit)
    {
        $this->limit = new PiAmount($limit);
    }

    /**
     * @return Asset
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * @param Asset $asset
     */
    public function setAsset($asset)
    {
        $this->asset = $asset;
    }
}
