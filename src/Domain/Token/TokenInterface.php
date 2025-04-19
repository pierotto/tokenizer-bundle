<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Domain\Token;

interface TokenInterface extends \JsonSerializable
{
    public static function createFromStdObject(\stdClass $token): self;
}
