<?php declare(strict_types = 1);

namespace Pierotto\TokenizerBundle\Command;

final class GenerateKeyCommand extends \Symfony\Component\Console\Command\Command
{

	public function __construct(
		private readonly string $passphrase,
		private readonly string $secretKey,
		private readonly string $publicKey,
		private readonly \Pierotto\TokenizerBundle\Algorithm\Algorithm $algorithm,
		private readonly \Symfony\Component\Filesystem\Filesystem $filesystem
	)
	{
		parent::__construct('tokenizer:generate:key');
	}


	protected function configure(): void
	{
		$this->setDescription('Generate public and private keys for use in application.');
	}


	protected function execute(
		\Symfony\Component\Console\Input\InputInterface $input,
		\Symfony\Component\Console\Output\OutputInterface $output
	): int
	{
		$alreadyExists = $this->filesystem->exists($this->secretKey) || $this->filesystem->exists($this->publicKey);
		if ($alreadyExists) {
			$output->writeln('Key already exist!');

			return 1;
		}

		[$secretKey, $publicKey] = $this->generateKeyPair();

		$this->filesystem->dumpFile($this->secretKey, $secretKey);
		$this->filesystem->dumpFile($this->publicKey, $publicKey);

		$output->writeln('Done!');

		return 0;
	}


	/**
	 * @return array<string>
	 */
	private function generateKeyPair(): array
	{
		$resource = \openssl_pkey_new($this->algorithm->toArray());
		if ($resource === FALSE) {
			throw new \RuntimeException(\openssl_error_string());
		}

		$success = \openssl_pkey_export($resource, $privateKey, $this->passphrase);

		if ($success === FALSE) {
			throw new \RuntimeException(\openssl_error_string());
		}

		$publicKeyData = \openssl_pkey_get_details($resource);

		if ($publicKeyData === FALSE) {
			throw new \RuntimeException(\openssl_error_string());
		}

		$publicKey = $publicKeyData['key'];

		return [$privateKey, $publicKey];
	}

}
