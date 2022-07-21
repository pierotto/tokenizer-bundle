Tokenizer Bundle

The package is used for secure data exchange between two parties using a JWT token.

## Installation

Require the bundle and its dependencies with composer:

`$ composer require pierotto/tokenizer-bundle`

Register the bundle:

```php
// app/AppKernel.php
public function registerBundles(): array
{
    $bundles = [
        new \Pierotto\TokenizerBundle\TokenizerBundle(),
    ];
}
```

Setup configuration:

```yml
tokenizer:
    algorithm: 'RS256'
    secret_key: '%kernel.project_dir%/app/config/keys/secret.key'
    public_key: '%kernel.project_dir%/app/config/keys/public.key'
    pass_phrase: 'abcd123'
```

Generate the public and private key using the console command:

```
php bin/console tokenizer:generate:key
```

## Usage

Start with `\Pierotto\TokenizerBundle\Tokenizer\TokenInterface` interface implementation. 
Object of this class represents data, which are encoded into shared token.

```php
<?php declare(strict_types = 1);

namespace MyProject\Token;

class TokenClass implements \Pierotto\TokenizerBundle\Tokenizer\TokenInterface
{
	public function __construct(
		private readonly int $user
	)
	{
	}

	public static function createFromStdObject(\stdClass $token): static
	{
		return new self(
			$token->user
		);
	}

	public function jsonSerialize(): array
	{
		return [
			'user' => $this->user,
		];
	}

	public function getUser(): int
	{
		return $this->user;
	}
}
```

Now you can tokenize your object class with tokenizer. The output is a token, which can then be converted back into an object using the `decode` method.

```
$token = $this->tokenizer->create(new \MyProject\Token\TokenClass(1));
```
