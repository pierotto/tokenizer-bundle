<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Infrastructure\Symfony\DependencyInjection;

use Pierotto\TokenizerBundle\Application\Factory\AlgorithmFactory;
use Pierotto\TokenizerBundle\Application\Token\Tokenizer;
use Pierotto\TokenizerBundle\Domain\Model\AlgorithmDefinition;
use Pierotto\TokenizerBundle\Domain\Token\TokenEncoderInterface;
use Pierotto\TokenizerBundle\Infrastructure\Jwt\TokenEncoder;
use Pierotto\TokenizerBundle\Infrastructure\OpenSsl\KeyGenerator;
use Pierotto\TokenizerBundle\Infrastructure\Symfony\Command\GenerateKeysCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class TokenizerExtension extends Extension
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration($this->getAlias());
        $config = $this->processConfiguration($configuration, $configs);

        $algorithms = [
            'RS256' => ['digest' => 'sha256', 'key_bit' => 2048, 'key_type' => \OPENSSL_KEYTYPE_RSA],
            'RS384' => ['digest' => 'sha384', 'key_bit' => 2048, 'key_type' => \OPENSSL_KEYTYPE_RSA],
            'RS512' => ['digest' => 'sha512', 'key_bit' => 4096, 'key_type' => \OPENSSL_KEYTYPE_RSA],
            'HS256' => ['digest' => 'sha256', 'key_bit' => 384, 'key_type' => \OPENSSL_KEYTYPE_DH],
            'HS384' => ['digest' => 'sha384', 'key_bit' => 384, 'key_type' => \OPENSSL_KEYTYPE_DH],
            'HS512' => ['digest' => 'sha512', 'key_bit' => 512, 'key_type' => \OPENSSL_KEYTYPE_DH],
            'ES256' => ['digest' => 'sha256', 'key_bit' => 384, 'key_type' => \OPENSSL_KEYTYPE_EC, 'curve' => 'secp256k1'],
            'ES384' => ['digest' => 'sha384', 'key_bit' => 512, 'key_type' => \OPENSSL_KEYTYPE_EC, 'curve' => 'secp384r1'],
            'ES512' => ['digest' => 'sha512', 'key_bit' => 1024, 'key_type' => \OPENSSL_KEYTYPE_EC, 'curve' => 'secp521r1'],
        ];

        $container->setParameter('tokenizer_algorithms', $algorithms);

        $container->register(KeyGenerator::class);

        $container->register(AlgorithmFactory::class)
            ->setArgument('$algorithms', '%tokenizer_algorithms%');

        $container->register(AlgorithmDefinition::class)
            ->setFactory([new Reference(AlgorithmFactory::class), 'create'])
            ->setArguments([$config['algorithm']]);

        $container->register(Tokenizer::class)
            ->setArgument('$encoder', new Reference(TokenEncoderInterface::class))
            ->setPublic(true);

        $container->register(TokenEncoderInterface::class, TokenEncoder::class)
            ->setArgument('$privateKeyFile', $config['private_key'])
            ->setArgument('$publicKeyFile', $config['public_key'])
            ->setArgument('$passphrase', $config['passphrase'])
            ->setArgument('$algorithm', new Reference(AlgorithmDefinition::class));

        $container->register(GenerateKeysCommand::class)
            ->setArgument('$passphrase', $config['passphrase'])
            ->setArgument('$privateKey', $config['private_key'])
            ->setArgument('$publicKey', $config['public_key'])
            ->setArgument('$algorithmDefinition', new Reference(AlgorithmDefinition::class))
            ->setArgument('$filesystem', new Reference('filesystem'))
            ->setArgument('$keyGenerator', new Reference(KeyGenerator::class))
            ->addTag('console.command')
            ->setPublic(true);
    }
}
