<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Validator;

class HashValidator extends Validator
{
    /**
     * @Assert\Type("string")
     * @Assert\Regex("/^\d{14}$/")
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    protected $hash;
}