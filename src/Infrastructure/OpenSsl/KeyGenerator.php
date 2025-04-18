<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Infrastructure\OpenSsl;

use Pierotto\TokenizerBundle\Domain\Model\AlgorithmDefinition;

class KeyGenerator
{
    /**
     * @return array{private: string, public: string}
     */
    public function generate(AlgorithmDefinition $algorithmDefinition, ?string $passphrase): array
    {
        $resource = \openssl_pkey_new($algorithmDefinition->toOpenSslConfig());
        if (false === $resource) {
            throw new \RuntimeException(\openssl_error_string() ?: 'Unknown OpenSSL error');
        }

        $success = \openssl_pkey_export($resource, $privateKey, $passphrase);
        if (false === $success) {
            throw new \RuntimeException(\openssl_error_string() ?: 'Unknown OpenSSL error');
        }

        $publicKeyData = \openssl_pkey_get_details($resource);
        if (false === $publicKeyData) {
            throw new \RuntimeException(\openssl_error_string() ?: 'Unknown OpenSSL error');
        }

        $publicKey = $publicKeyData['key'];

        return ['private' => $privateKey, 'public' => $publicKey];
    }
}
