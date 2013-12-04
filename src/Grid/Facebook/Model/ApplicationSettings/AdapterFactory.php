<?php

namespace Grid\Facebook\Model\ApplicationSettings;

use Zork\Factory\Builder;
use Zork\Factory\FactoryAbstract;
use Zork\Model\ModelAwareTrait;
use Zork\Model\ModelAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zork\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * \User\Model\Authentication\AdapterFactory
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdapterFactory extends FactoryAbstract
                  implements ModelAwareInterface
{

    use ModelAwareTrait,
        ServiceLocatorAwareTrait;

    /**
     * Constructor
     *
     * @param   Builder                 $factoryBuilder
     * @param   Model                   $facebookApplicationSettingsModel
     * @param   ServiceLocatorInterface $serviceLocator
     */
    public function __construct( Builder                    $factoryBuilder,
                                 Model                      $facebookApplicationSettingsModel,
                                 ServiceLocatorInterface    $serviceLocator )
    {
        parent::__construct( $factoryBuilder );
        $this->setModel( $facebookApplicationSettingsModel )
             ->setServiceLocator( $serviceLocator );
    }

    /**
     * Factory an object
     *
     * @param   string|object|array $adapter
     * @param   object|array|null   $options
     * @return  AdapterInterface
     */
    public function factory( $adapter, $options = null )
    {
        $adapter = parent::factory( $adapter, $options );
        $adapter->setModel( $this->getModel() );

        if ( $adapter instanceof ServiceLocatorAwareInterface )
        {
            $adapter->setServiceLocator( $this->getServiceLocator() );
        }

        return $adapter;
    }

    /**
     * Get default settings' keys
     *
     * @return  array
     */
    public function getDefaultSettingsKeys()
    {
        $keys = array();

        foreach ( $this->getRegisteredAdapters() as $adapter )
        {
            $keys = array_merge( $keys, $adapter::getDefaultSettingsKeys() );
        }

        return array_unique( $keys );
    }

    /**
     * Is this factory have extensions? (non-fallback adapters)
     *
     * @return  bool
     */
    public function haveExtensions()
    {
        foreach ( $this->getRegisteredAdapters() as $adapter )
        {
            if ( $adapter == ( __NAMESPACE__ . '\DefaultAdapter' ) )
            {
                continue;
            }

            return true;
        }

        return false;
    }

}
