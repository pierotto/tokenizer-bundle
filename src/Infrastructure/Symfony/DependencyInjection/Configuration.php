<?php

declare(strict_types=1);

namespace Pierotto\TokenizerBundle\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function __construct(
        private readonly string $alias,
    ) {
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->alias);
        $treeBuilder->getRootNode() // @phpstan-ignore method.notFound
            ->children()
                ->scalarNode('algorithm')->defaultValue('RS256')->end()
                ->scalarNode('private_key')->isRequired()->end()
                ->scalarNode('public_key')->isRequired()->end()
                ->scalarNode('passphrase')->defaultValue('')->end()
            ->end();

        return $treeBuilder;
    }
}
