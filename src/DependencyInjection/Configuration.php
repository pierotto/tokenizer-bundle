<?php declare(strict_types = 1);

namespace Pierotto\TokenizerBundle\DependencyInjection;

class Configuration implements \Symfony\Component\Config\Definition\ConfigurationInterface
{

	public function __construct(
		private readonly string $alias
	)
	{
	}


	public function getConfigTreeBuilder(): \Symfony\Component\Config\Definition\Builder\TreeBuilder
	{
		$treeBuilder = new \Symfony\Component\Config\Definition\Builder\TreeBuilder($this->alias);
		$rootNode = $treeBuilder->getRootNode();

		$rootNode
			->children()
				->scalarNode('algorithm')
					->defaultValue('RS256')
				->end()
				->scalarNode('secret_key')->isRequired()->end()
				->scalarNode('public_key')->isRequired()->end()
				->scalarNode('pass_phrase')->defaultValue('')->end()
			->end()
		;

		return $treeBuilder;
	}

}
