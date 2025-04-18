<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Domain\Token;

use Pierotto\TokenizerBundle\Domain\Exception\ExpiredTokenException;
use Pierotto\TokenizerBundle\Domain\Exception\TokenDecodeException;

interface TokenEncoderInterface
{
    public function encode(TokenInterface $token): string;

    /**
     * @param class-string $class
     *
     * @throws ExpiredTokenException
     * @throws TokenDecodeException
     */
    public function decode(string $token, string $class): TokenInterface;
}
