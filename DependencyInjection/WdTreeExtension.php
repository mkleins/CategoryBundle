<?php

namespace Wd\TreeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

use Symfony\Component\DependencyInjection\Definition;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class WdTreeExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->addDefaultTree($config);
        $container->setParameter('wd_tree.configs', $config);


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    private function addDefaultTree(&$config)
    {
        $config['trees']['__default'] = array(
            'label_template' => 'WdTreeBundle::label.html.twig',
            'controller' => 'WdTreeBundle:Default',
            'editable_root' => false,
            'is_searchable' => false,
            'is_selectable' => false,
            'is_sortable' => true,
            'theme' => 'default',
            'assets_manager' => 'assetic',
            'root_icon' => 'bundles/wdtree/images/database.png',
            'folder_icon' => 'bundles/wdtree/images/folder.png',
            'node_icon' => 'bundles/wdtree/images/folder.png'
        );
    }
}
