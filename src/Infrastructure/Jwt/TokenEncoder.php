<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Infrastructure\Jwt;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Pierotto\TokenizerBundle\Domain\Exception\ExpiredTokenException;
use Pierotto\TokenizerBundle\Domain\Exception\KeyLoadingException;
use Pierotto\TokenizerBundle\Domain\Exception\TokenDecodeException;
use Pierotto\TokenizerBundle\Domain\Model\AlgorithmDefinition;
use Pierotto\TokenizerBundle\Domain\Token\TokenEncoderInterface;
use Pierotto\TokenizerBundle\Domain\Token\TokenInterface;

final class TokenEncoder implements TokenEncoderInterface
{
    private \OpenSSLAsymmetricKey $privateKey;

    private \OpenSSLAsymmetricKey $publicKey;

    public function __construct(
        private readonly string $privateKeyFile,
        private readonly string $publicKeyFile,
        private readonly string $passphrase,
        private readonly AlgorithmDefinition $algorithm,
    ) {
    }

    public function encode(TokenInterface $token): string
    {
        return JWT::encode($token->jsonSerialize(), $this->getPrivateKey(), $this->algorithm->name);
    }

    public function decode(string $token, string $class): TokenInterface
    {
        try {
            $content = JWT::decode($token, new Key($this->getPublicKey(), $this->algorithm->name));
        } catch (ExpiredException $e) {
            throw new ExpiredTokenException('The provided token has expired. Please request a new token.', previous: $e);
        } catch (\Throwable $e) {
            throw new TokenDecodeException(\sprintf('An error occurred while decoding the token: [%s].', $e->getMessage()), previous: $e);
        }

        if (!\method_exists($class, 'createFromStdObject')) {
            throw new TokenDecodeException(\sprintf(
                'Class [%s] must implement static method createFromStdObject().',
                $class,
            ));
        }

        return $class::createFromStdObject($content);
    }

    protected function getPrivateKey(): \OpenSSLAsymmetricKey
    {
        if (false === isset($this->privateKey)) {
            $content = \openssl_pkey_get_private('file://' . $this->privateKeyFile, $this->passphrase);
            if (false === $content) {
                throw new KeyLoadingException(\sprintf('Failed to load private key from [%s].', $this->privateKeyFile));
            }

            $this->privateKey = $content;
        }

        return $this->privateKey;
    }

    protected function getPublicKey(): \OpenSSLAsymmetricKey
    {
        if (false === isset($this->publicKey)) {
            $content = \openssl_pkey_get_public('file://' . $this->publicKeyFile);
            if (false === $content) {
                throw new KeyLoadingException(\sprintf('Failed to load public key from [%s].', $this->publicKeyFile));
            }

            $this->publicKey = $content;
        }

        return $this->publicKey;
    }
}
