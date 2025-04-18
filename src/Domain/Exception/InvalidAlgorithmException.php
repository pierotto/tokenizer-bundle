<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Domain\Exception;

class InvalidAlgorithmException extends \InvalidArgumentException
{
    /**
     * @param array<string> $available
     */
    public static function unknownName(string $name, array $available): self
    {
        return new self(\sprintf(
            'Invalid algorithm name "%s" in tokenizer configuration. Choose one of: %s.',
            $name,
            \implode(', ', $available),
        ));
    }
}
