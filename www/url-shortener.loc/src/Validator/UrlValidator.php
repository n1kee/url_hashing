<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Validator;

class UrlValidator extends Validator
{
    /**
     * @Assert\Type("string")
     * @Assert\Regex("|^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$|")
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    protected $url;
}