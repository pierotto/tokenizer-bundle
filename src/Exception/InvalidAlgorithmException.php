<?php declare(strict_types = 1);

namespace Pierotto\TokenizerBundle\Exception;

class InvalidAlgorithmException extends \InvalidArgumentException
{

	/**
	 * @param string $algorithm
	 * @param array<string> $algorithms
	 */
	public function __construct(
		string $algorithm,
		array $algorithms
	)
	{
		parent::__construct(
			\sprintf('Invalid algorithm name (%s) in tokenizer algorithm configuration", choose one of %s', $algorithm, \implode(', ', $algorithms))
		);
	}

}
