<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Tests\Tokenizer;

use PHPUnit\Framework\TestCase;
use Pierotto\TokenizerBundle\Domain\Model\AlgorithmDefinition;
use Pierotto\TokenizerBundle\Infrastructure\Jwt\TokenEncoder;
use Pierotto\TokenizerBundle\Infrastructure\OpenSsl\KeyGenerator;

final class TokenizerTest extends TestCase
{
    private TokenEncoder $tokenizer;

    private string $privateKeyFile;

    private string $publicKeyFile;

    protected function setUp(): void
    {
        $algorithm = new AlgorithmDefinition(
            name: 'RS256',
            digest: 'sha256',
            keyBit: 2048,
            keyType: \OPENSSL_KEYTYPE_RSA,
        );

        $keyGenerator = new KeyGenerator();
        $keys = $keyGenerator->generate($algorithm, 'test');

        $this->privateKeyFile = \tempnam(\sys_get_temp_dir(), 'jwt_priv_');
        $this->publicKeyFile = \tempnam(\sys_get_temp_dir(), 'jwt_pub_');

        \file_put_contents($this->privateKeyFile, $keys['private']);
        \file_put_contents($this->publicKeyFile, $keys['public']);

        $this->tokenizer = new TokenEncoder(
            privateKeyFile: $this->privateKeyFile,
            publicKeyFile: $this->publicKeyFile,
            passphrase: 'test',
            algorithm: $algorithm,
        );
    }

    public function testEncodeAndDecode(): void
    {
        $original = new DummyToken(42);
        $token = $this->tokenizer->encode($original);

        /** @var DummyToken $decoded */
        $decoded = $this->tokenizer->decode($token, DummyToken::class);

        $this->assertSame(42, $decoded->getUser());
    }

    protected function tearDown(): void
    {
        @\unlink($this->privateKeyFile);
        @\unlink($this->publicKeyFile);
    }
}
