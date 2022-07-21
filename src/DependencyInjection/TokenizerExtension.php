<?php declare(strict_types = 1);

namespace Pierotto\TokenizerBundle\DependencyInjection;

class TokenizerExtension extends \Symfony\Component\DependencyInjection\Extension\Extension
{

	/**
	 * @throws \Exception
	 */
	public function load(
		array $configs,
		\Symfony\Component\DependencyInjection\ContainerBuilder $container
	): void
	{
		$loader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader(
			$container,
			new \Symfony\Component\Config\FileLocator([__DIR__ . '/../Resources/config'])
		);
		$loader->load('services.yml');

		$configuration = new \Pierotto\TokenizerBundle\DependencyInjection\Configuration(
			$this->getAlias()
		);
		$config = $this->processConfiguration($configuration, $configs);

		foreach ($config as $key => $value) {
			$container->setParameter(\sprintf('%s.%s', $this->getAlias(), $key), $value);
		}
	}

}
