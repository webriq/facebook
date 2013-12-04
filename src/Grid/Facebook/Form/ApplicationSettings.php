<?php

namespace Grid\Facebook\Form;

use ArrayIterator;
use AppendIterator;
use Zork\Form\Form;
use Zend\Form\FieldsetInterface;
use Zork\Form\PrepareElementsAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * ApplicationSettings form
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ApplicationSettings extends Form
                       implements PrepareElementsAwareInterface,
                                  ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;

    /**
     * Prepare additional elements for the form
     *
     * @return void
     */
    public function prepareElements()
    {
        if ( ! $this->has( 'default' ) )
        {
            return;
        }

        $default = $this->get( 'default' );

        if ( ! $default instanceof FieldsetInterface )
        {
            throw new \LogicException( sprintf(
                '%s: "%s" must be an instance of %s - instead %s given',
                __METHOD__,
                'default',
                'Zend\Form\FieldsetInterface',
                get_class( $default )
            ) );
        }

        /* @var $adapterFactory \Grid\Facebook\Model\ApplicationSettings\AdapterFactory */
        $serviceLocator = $this->getServiceLocator();
        $adapterFactory = $serviceLocator->get(
            'Grid\Facebook\Model\ApplicationSettings\AdapterFactory'
        );

        /* @var $default FieldsetInterface */
        $count      = 0;
        $children   = new AppendIterator();
        $elements   = $default->getElements();
        $fieldsets  = $default->getFieldsets();
        $keys       = $adapterFactory->getDefaultSettingsKeys();

        if ( empty( $elements ) )
        {
            $elements = array();
        }

        if ( empty( $fieldsets ) )
        {
            $fieldsets = array();
        }

        if ( is_array( $elements ) )
        {
            $elements = new ArrayIterator( $elements );
        }

        if ( is_array( $fieldsets ) )
        {
            $fieldsets = new ArrayIterator( $fieldsets );
        }

        $children->append( $elements );
        $children->append( $fieldsets );

        foreach ( $children as $child )
        {
            /* @var $child \Zend\Form\ElementInterface */
            $name = $child->getName();

            if ( ! in_array( $name, $keys ) )
            {
                $default->remove( $name );
            }
            else
            {
                $count++;
            }
        }

        if ( $count < 1 )
        {
            $this->remove( 'default' );
        }
    }

}
