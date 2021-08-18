<?php


namespace PiSdk\Api\XdrModel\Operation;

use phpseclib3\Math\BigInteger;
use PiSdk\Api\Keypair;
use PiSdk\Api\Model\PiAmount;
use PiSdk\Api\Xdr\XdrBuffer;
use PiSdk\Api\Xdr\XdrEncoder;
use PiSdk\Api\XdrModel\AccountId;

/**
 * Creates and funds a new account
 *
 * See: https://www.stellar.org/developers/guides/concepts/list-of-operations.html#create-account
 */
class CreateAccountOp extends Operation
{
    /**
     * @var AccountId
     */
    protected $newAccount;

    /**
     * @var PiAmount
     */
    protected $startingBalance;

    /**
     * @param AccountId      $newAccount
     * @param int|BigInteger $startingBalance int representing lumens or BigInteger representing stroops
     * @param null|string|Keypair|AccountId $sourceAccountId
     * @throws \ErrorException
     */
    public function __construct(AccountId $newAccount, $startingBalance, $sourceAccount = null)
    {
        if ($sourceAccount instanceof Keypair) {
            $sourceAccount = new AccountId($sourceAccount->getPublicKey());
        }
        if (is_string($sourceAccount)) {
            $sourceAccount = new AccountId($sourceAccount);
        }

        parent::__construct( Operation::TYPE_CREATE_ACCOUNT, $sourceAccount);

        $this->newAccount = $newAccount;
        $this->startingBalance = new PiAmount($startingBalance);
    }

    /**
     * @return string XDR bytes
     */
    public function toXdr()
    {
        $bytes = parent::toXdr();

        $bytes .= $this->newAccount->toXdr();
        $bytes .= XdrEncoder::signedBigInteger64($this->startingBalance->getUnscaledBigInteger());

        return $bytes;
    }

    /**
     * @param XdrBuffer $xdr
     * @return CreateAccountOp|Operation
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $newAccountId = AccountId::fromXdr($xdr);
        $startingBalance = new BigInteger($xdr->readInteger64());

        return new CreateAccountOp($newAccountId, $startingBalance);
    }

    /**
     * @return AccountId
     */
    public function getNewAccount()
    {
        return $this->newAccount;
    }

    /**
     * @param AccountId $newAccount
     */
    public function setNewAccount($newAccount)
    {
        $this->newAccount = $newAccount;
    }

    /**
     * @return PiAmount
     */
    public function getStartingBalance()
    {
        return $this->startingBalance;
    }

    /**
     * @param PiAmount $startingBalance
     */
    public function setStartingBalance($startingBalance)
    {
        $this->startingBalance = $startingBalance;
    }
}
