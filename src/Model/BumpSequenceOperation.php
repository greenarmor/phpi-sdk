<?php


namespace Greenarmor\\PiSdk\Model;


use phpseclib3\Math\BigInteger;
use Greenarmor\\PiSdk\XdrModel\Operation\BumpSequenceOp;

class BumpSequenceOperation extends Operation
{
    /**
     * @var BigInteger
     */
    protected $bumpTo;

    /**
     * @param array $rawData
     * @return BumpSequenceOp
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new BumpSequenceOp($rawData['id'], $rawData['type']);

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    /**
     * @param $id
     * @param $type
     */
    public function __construct($id, $type)
    {
        parent::__construct($id, Operation::TYPE_BUMP_SEQUENCE);
    }

    /**
     * @param $rawData
     */
    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        $this->bumpTo = new BigInteger($rawData['bump_to']);
    }
}
