<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Infrastructure\Symfony\Command;

use Pierotto\TokenizerBundle\Domain\Model\AlgorithmDefinition;
use Pierotto\TokenizerBundle\Infrastructure\OpenSsl\KeyGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

final class GenerateKeysCommand extends Command
{
    public function __construct(
        private readonly string $passphrase,
        private readonly string $privateKey,
        private readonly string $publicKey,
        private readonly AlgorithmDefinition $algorithmDefinition,
        private readonly Filesystem $filesystem,
        private readonly KeyGenerator $keyGenerator,
    ) {
        parent::__construct('tokenizer:generate:keys');
    }

    protected function configure(): void
    {
        $this->setDescription('Generate a new private and public key pair based on configured algorithm.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $alreadyExists = $this->filesystem->exists($this->privateKey) || $this->filesystem->exists($this->publicKey);
        if ($alreadyExists) {
            $output->writeln('Key already exist!');

            return self::FAILURE;
        }

        $keys = $this->keyGenerator->generate($this->algorithmDefinition, $this->passphrase);

        $this->filesystem->dumpFile($this->privateKey, $keys['private']);
        $this->filesystem->dumpFile($this->publicKey, $keys['public']);

        $output->writeln('Done!');

        return self::SUCCESS;
    }
}
