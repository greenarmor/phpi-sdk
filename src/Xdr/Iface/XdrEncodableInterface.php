<?php


namespace PiSdk\Api\Xdr\Iface;


interface XdrEncodableInterface
{
    /**
     * Returns the binary representation of the object in XDR format
     *
     * @return string
     */
    public function toXdr();
}