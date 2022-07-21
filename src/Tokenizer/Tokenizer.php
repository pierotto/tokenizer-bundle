<?php declare(strict_types = 1);

namespace Pierotto\TokenizerBundle\Tokenizer;

class Tokenizer
{

	private \OpenSSLAsymmetricKey $secretKey;

	private \OpenSSLAsymmetricKey $publicKey;


	public function __construct(
		string $secretKey,
		string $publicKey,
		string $passphrase,
		private readonly \Pierotto\TokenizerBundle\Algorithm\Algorithm $algorithm
	)
	{
		$this->secretKey = \openssl_pkey_get_private('file://' . $secretKey, $passphrase);
		$this->publicKey = \openssl_pkey_get_public('file://' . $publicKey);
	}


	public function create(
		\Pierotto\TokenizerBundle\Tokenizer\TokenInterface $object
	): string
	{
		return \Firebase\JWT\JWT::encode($object->jsonSerialize(), $this->secretKey, $this->algorithm->getAlgorithm());
	}


	public function decode(
		string $token
	): \stdClass
	{
		return \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($this->publicKey, $this->algorithm->getAlgorithm()));
	}
}
