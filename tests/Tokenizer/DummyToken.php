<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Tests\Tokenizer;

use Pierotto\TokenizerBundle\Domain\Token\TokenInterface;

final class DummyToken implements TokenInterface
{
    public function __construct(
        private readonly int $user,
    ) {
    }

    public static function createFromStdObject(\stdClass $token): self
    {
        return new self($token->user);
    }

    /**
     * @return int[]
     */
    public function jsonSerialize(): array
    {
        return ['user' => $this->user];
    }

    public function getUser(): int
    {
        return $this->user;
    }
}
