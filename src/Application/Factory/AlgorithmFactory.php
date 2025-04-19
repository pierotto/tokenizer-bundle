<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Application\Factory;

use Pierotto\TokenizerBundle\Domain\Exception\InvalidAlgorithmException;
use Pierotto\TokenizerBundle\Domain\Model\AlgorithmDefinition;

final class AlgorithmFactory
{
    /**
     * @param array<string, mixed> $algorithms
     */
    public function __construct(
        private readonly array $algorithms,
    ) {
    }

    public function create(string $name): AlgorithmDefinition
    {
        if (!isset($this->algorithms[$name])) {
            throw InvalidAlgorithmException::unknownName($name, \array_keys($this->algorithms));
        }

        $config = $this->algorithms[$name];

        return new AlgorithmDefinition(
            name: $name,
            digest: $config['digest'],
            keyBit: $config['key_bit'],
            keyType: $config['key_type'],
            curve: $config['curve'] ?? null,
        );
    }
}
