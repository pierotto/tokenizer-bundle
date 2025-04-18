<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Tests\Algorithm;

use PHPUnit\Framework\TestCase;
use Pierotto\TokenizerBundle\Application\Factory\AlgorithmFactory;
use Pierotto\TokenizerBundle\Domain\Exception\InvalidAlgorithmException;

final class AlgorithmFactoryTest extends TestCase
{
    private AlgorithmFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new AlgorithmFactory([
            'RS256' => [
                'digest' => 'sha256',
                'key_bit' => 2048,
                'key_type' => \OPENSSL_KEYTYPE_RSA,
            ],
            'ES256' => [
                'digest' => 'sha256',
                'key_bit' => 384,
                'key_type' => \OPENSSL_KEYTYPE_EC,
                'curve' => 'secp256k1',
            ],
        ]);
    }

    public function testCreateReturnsCorrectAlgorithmDefinition(): void
    {
        $definition = $this->factory->create('RS256');

        $this->assertSame('RS256', $definition->name);
        $this->assertSame('sha256', $definition->digest);
        $this->assertSame(2048, $definition->keyBit);
        $this->assertSame(\OPENSSL_KEYTYPE_RSA, $definition->keyType);
        $this->assertNull($definition->curve);
    }

    public function testCreateWithCurve(): void
    {
        $definition = $this->factory->create('ES256');

        $this->assertSame('secp256k1', $definition->curve);
    }

    public function testCreateThrowsExceptionOnUnknownName(): void
    {
        $this->expectException(InvalidAlgorithmException::class);
        $this->expectExceptionMessage('Invalid algorithm name "XXX" in tokenizer configuration. Choose one of: RS256, ES256.');

        $this->factory->create('XXX');
    }
}
