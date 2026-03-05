<?php

namespace App\Services\Im\Entity;

interface ImMessageData extends \JsonSerializable
{
    public function toArray();
}
