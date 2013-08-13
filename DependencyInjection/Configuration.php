<?php

namespace IMOControl\M3\ProjectBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('imo_control_m3_project', 'array');
		$rootNode
			->children()
				->scalarNode('use_project_folder')->defaultValue(true)->end()
				->scalarNode('symlink_project_folder')->defaultValue(true)->end()
				->scalarNode('project_folder_root_dir')->defaultValue("%kernel.root_dir%")->end()
            	->arrayNode('class')
            		->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('customer')->cannotBeEmpty()->defaultValue('IMOControl\M3\CustomerBundle\Entity\Customer')->end()
                        ->scalarNode('invoice')->cannotBeEmpty()->defaultValue('IMOControl\M3\InvoiceBundle\Entity\Invoice')->end()
                    ->end()
                ->end()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('project')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('IMOControl\M3\ProjectBundle\Admin\ProjectAdmin')->end()
                                ->scalarNode('entity')->cannotBeEmpty()->defaultValue('IMOControl\M3\ProjectBundle\Entity\Project')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('IMOControlM3ProjectBundle:Project')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('default')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
    
    public function getName()
    {
    	return 'imocontrol_project';
    }
}
