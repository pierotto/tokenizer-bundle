<?php declare(strict_types = 1);

namespace Pierotto\TokenizerBundle\Algorithm;

final class Algorithm
{

	public function __construct(
		private readonly string $algorithm,
		private readonly string $digest,
		private readonly int $keyBit,
		private readonly int $keyType,
		private readonly ?string $curve = NULL
	)
	{
	}


	/**
	 * @return array<string, string|int>
	 */
	public function toArray(): array
	{
		$config = [
			'digest_alg' => $this->digest,
			'private_key_type' => $this->keyType,
			'private_key_bits' => $this->keyBit,
		];

		if ($this->curve !== NULL) {
			$config['curve_name'] = $this->curve;
		}

		return $config;
	}


	public function getAlgorithm(): string
	{
		return $this->algorithm;
	}

}
