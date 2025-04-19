<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Application\Token;

use Pierotto\TokenizerBundle\Domain\Exception\ExpiredTokenException;
use Pierotto\TokenizerBundle\Domain\Exception\TokenDecodeException;
use Pierotto\TokenizerBundle\Domain\Token\TokenEncoderInterface;
use Pierotto\TokenizerBundle\Domain\Token\TokenInterface;

class Tokenizer
{
    public function __construct(
        private readonly TokenEncoderInterface $encoder,
    ) {
    }

    public function encode(TokenInterface $token): string
    {
        return $this->encoder->encode($token);
    }

    /**
     * @param class-string $class
     *
     * @throws ExpiredTokenException
     * @throws TokenDecodeException
     */
    public function decode(string $token, string $class): TokenInterface
    {
        return $this->encoder->decode($token, $class);
    }
}
