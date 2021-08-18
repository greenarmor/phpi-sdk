<?php


namespace PiSdk\Api\Xdr\Type;


use Countable;
use PiSdk\Api\Xdr\Iface\XdrEncodableInterface;
use PiSdk\Api\Xdr\XdrEncoder;

class VariableArray implements XdrEncodableInterface, Countable
{
    /**
     * @var XdrEncodableInterface[]
     */
    private $elements;

    public function __construct()
    {
        $this->elements = [];
    }

    public function append(XdrEncodableInterface $element)
    {
        $this->elements[] = $element;
    }

    public function toXdr()
    {
        $bytes = '';

        if (count($this->elements) > (pow(2, 32) - 1)) {
            throw new \ErrorException('Maximum number of elements exceeded');
        }

        $bytes .= XdrEncoder::unsignedInteger(count($this->elements));

        foreach ($this->elements as $element) {
            $bytes .= $element->toXdr();
        }

        return $bytes;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_values($this->elements);
    }
}
