<?php

namespace App\Services\IpInfo;

class Details
{
    public function __construct(array $rawDetails)
    {
        foreach ($rawDetails as $property => $value) {
            $this->$property = $value;
        }
        $this->all = $rawDetails;
    }

    public function __toString(): string {
        return json_encode($this);
    }
}
