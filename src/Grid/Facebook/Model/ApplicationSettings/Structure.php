<?php

namespace Grid\Facebook\Model\ApplicationSettings;

use Zend\Stdlib\ArrayUtils;
use Zork\Model\Structure\MapperAwareAbstract;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Structure extends MapperAwareAbstract
{

    /**
     * @const string
     */
    const DEFAULT_APPLICATION = 'default';

    /**
     * Application name
     *
     * @var string
     */
    protected $application = self::DEFAULT_APPLICATION;

    /**
     * Settings as array
     *
     * @var array
     */
    protected $settings = array();

    /**
     * Set settings
     *
     * @param   array|\Traversable  $settings
     * @return  Structure
     */
    public function setSettings( $settings )
    {
        $this->settings = ArrayUtils::iteratorToArray( $settings );
        return $this;
    }

    /**
     * Add (merge with existing) settings
     *
     * @param   array|\Traversable  $settings
     * @return  Structure
     */
    public function addSettings( $settings )
    {
        $this->settings = ArrayUtils::merge(
            $this->settings,
            ArrayUtils::iteratorToArray( $settings )
        );

        return $this;
    }

    /**
     * Has an application's setting available?
     *
     * @param   string  $name
     * @return  bool
     */
    public function hasSetting( $name )
    {
        return isset( $this->settings[$name] );
    }

    /**
     * Get an application's setting
     *
     * @param   string  $name
     * @param   mixed   $default
     * @return  bool
     */
    public function getSetting( $name, $default = null )
    {
        return isset( $this->settings[$name] )
             ? $this->settings[$name]
             : $default;
    }

    /**
     * Set an application's setting
     * 
     * @param   string      $name
     * @param   mixed|null  $value
     * @return  Structure
     */
    public function setSetting( $name, $value )
    {
        if ( null === $value )
        {
            unset( $this->settings[$name] );
        }
        else
        {
            $this->settings[$name] = $value;
        }

        return $this;
    }

}
