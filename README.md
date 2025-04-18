Tokenizer Bundle
================

A lightweight Symfony bundle for encoding and decoding data using JWT.  
It allows secure, structured communication between services or systems using tokenized data.

## Requirements

- PHP 8.3+
- Symfony 7.0+

## Installation

Install the bundle via Composer:

```bash
composer require pierotto/tokenizer-bundle
```

Register the bundle (if using Symfony without Flex):

```php
// bundles.php
return [
    Pierotto\TokenizerBundle\Infrastructure\Symfony\TokenizerBundle::class => ['all' => true],
];
```

## Configuration

Add the following configuration to your `config/packages/tokenizer.yaml`:

```yaml
tokenizer:
    algorithm: 'RS256'
    private_key: '%kernel.project_dir%/config/keys/private.key'
    public_key: '%kernel.project_dir%/config/keys/public.key'
    passphrase: 'abcd123'
```

## Key generation

Generate a private and public key pair using the console command:

```bash
php bin/console tokenizer:generate:keys
```

## Usage

Start by implementing the `\Pierotto\TokenizerBundle\Tokenizer\TokenInterface`.  
This object represents the data that will be encoded into a token.

```php
<?php declare(strict_types=1);

namespace App\Token;

use Pierotto\TokenizerBundle\Tokenizer\TokenInterface;

class TokenClass implements TokenInterface
{
    public function __construct(
        private readonly int $user,
    ) {}

    public static function createFromStdObject(\stdClass $token): self
    {
        return new self($token->user);
    }

    public function jsonSerialize(): array
    {
        return ['user' => $this->user];
    }

    public function getUser(): int
    {
        return $this->user;
    }
}
```

Now you can encode and decode your object using the tokenizer service:

```php
$token = $tokenizer->encode(new TokenClass(1));

/** @var TokenClass $object */
$object = $tokenizer->decode($token, TokenClass::class);
```
