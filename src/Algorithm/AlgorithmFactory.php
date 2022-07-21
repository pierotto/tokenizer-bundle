<?php declare(strict_types = 1);

namespace Pierotto\TokenizerBundle\Algorithm;

final class AlgorithmFactory
{

	/**
	 * @param string $algorithm
	 * @param array<string, mixed> $algorithms
	 */
	public function __construct(
		private readonly string $algorithm,
		private readonly array $algorithms
	)
	{
	}



	public function create(): \Pierotto\TokenizerBundle\Algorithm\Algorithm
	{
		foreach ($this->algorithms as $algorithm => $configuration) {
			if ($algorithm !== $this->algorithm) {
				continue;
			}

			return new \Pierotto\TokenizerBundle\Algorithm\Algorithm(
				$algorithm,
				$configuration['digest'],
				$configuration['key_bit'],
				$configuration['key_type'],
				$configuration['curve'] ?? NULL
			);
		}

		throw new \Pierotto\TokenizerBundle\Exception\InvalidAlgorithmException(
			$this->algorithm,
			\array_keys($this->algorithms)
		);
	}

}
