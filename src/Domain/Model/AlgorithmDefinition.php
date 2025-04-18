<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Domain\Model;

final class AlgorithmDefinition
{
    public function __construct(
        public readonly string $name,
        public readonly string $digest,
        public readonly int $keyBit,
        public readonly int $keyType,
        public readonly ?string $curve = null,
    ) {
    }

    /**
     * @return array<string, string|int>
     */
    public function toOpenSslConfig(): array
    {
        $config = [
            'digest_alg' => $this->digest,
            'private_key_bits' => $this->keyBit,
            'private_key_type' => $this->keyType,
        ];

        if (null !== $this->curve) {
            $config['curve_name'] = $this->curve;
        }

        return $config;
    }
}
