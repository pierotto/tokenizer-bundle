<?php declare(strict_types = 1);

namespace Pierotto\TokenizerBundle\Tokenizer;

interface TokenInterface extends \JsonSerializable
{

	public static function createFromStdObject(\stdClass $token): self;

}
