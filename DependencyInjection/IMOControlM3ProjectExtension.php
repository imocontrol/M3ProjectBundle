<?php

namespace IMOControl\M3\ProjectBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
//use IMOControl\M3\ExtensionBundle\Mapper\DoctrineCollector;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

class IMOControlM3ProjectExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

		$this->initApplicationConfig($config, $container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('admin_services.yml');
        
        $this->registerDoctrineMappings($config);
        
        //echo "<pre>" . print_r($container->getParameter('imocontrol.project.folder.root_dir'), true) . "</pre>";
        //die();
    }
	
	protected function registerDoctrineMappings($config)
	{
	
        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['admin']['project']['entity'], 'mapManyToOne', array(
            'fieldName'     => 'customer',
            'targetEntity'  => $config['class']['customer'],
            'cascade'       => array(
                'all',
            ),
            'mappedBy'      => null,
            'inversedBy'    => null,
            'orphanRemoval' => false,
        ));
	}
	
	protected function initApplicationConfig($config, $container) 
	{
		$container->setParameter('imocontrol.project.folder.root_dir', $config['project_folder_root_dir']);
		$container->setParameter('imocontrol.project.admin.class', $config['admin']['project']['class']);
		$container->setParameter('imocontrol.project.admin.entity.class', $config['admin']['project']['entity']);
		$container->setParameter('imocontrol.project.admin.controller.class', $config['admin']['project']['controller']);
		$container->setParameter('imocontrol.project.admin.translation', $config['admin']['project']['translation']);
		
	}
	
	
	
	public function prepend(ContainerBuilder $container)
	{
	    /*
	    // get all Bundles
	    $bundles = $container->getParameter('kernel.bundles');
	    // determine if AcmeGoodbyeBundle is registered
	    if (!isset($bundles['IMOControlM3ContractBundle'])) {
	        // disable AcmeGoodbyeBundle in Bundles
	        $config = array('use_contract_modus' => false);
	        foreach ($container->getExtensions() as $name => $extension) {
	            switch ($name) {
	                case 'acme_something':
	                case 'acme_other':
	                    // set use_acme_goodbye to false in the config of acme_something and acme_other
	                    // note that if the user manually configured use_acme_goodbye to true in the
	                    // app/config/config.yml then the setting would in the end be true and not false
	                    $container->prependExtensionConfig($name, $config);
	                    break;
	            }
	        }
	    }
	
	    // process the configuration of AcmeHelloExtension
	    $configs = $container->getExtensionConfig($this->getAlias());
	    // use the Configuration class to generate a config array with the settings ``acme_hello``
	    $config = $this->processConfiguration(new Configuration(), $configs);
	
	    // check if entity_manager_name is set in the ``acme_hello`` configuration
	    if (isset($config['entity_manager_name'])) {
	        // prepend the acme_something settings with the entity_manager_name
	        $config = array('entity_manager_name' => $config['entity_manager_name']);
	        $container->prependExtensionConfig('acme_something', $config);
	    }
		 
		 // */
	}
	
	protected function initProjectFolders()
	{

	}
	
	protected function initContractBundle()
	{
		
	}
}
