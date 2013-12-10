<?php

namespace Grid\Facebook\Model\ApplicationSettings;

use Zork\Model\Exception;
use Zork\Model\ModelAwareTrait;
use Zork\Model\Structure\StructureAbstract;

/**
 * DefaultAdapter
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractAdapter extends StructureAbstract
                            implements AdapterInterface
{

    use ModelAwareTrait;

    /**
     * @const string
     * @abstract
     */
    const APPLICATION = Structure::DEFAULT_APPLICATION;

    /**
     * @const string
     */
    const FALLBACK = Structure::DEFAULT_APPLICATION;

    /**
     * @const string
     */
    const MODE_DEFAULT = 'default';

    /**
     * @const string
     */
    const MODE_SPECIFIC = 'specific';

    /**
     * @var Structure
     */
    protected $fallbackStructure;

    /**
     * @var Structure
     */
    protected $specificStructure;

    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     *
     * @param   array   $options;
     * @return  float
     */
    public static function acceptsOptions( array $options )
    {
        return isset( $options['application'] )
            && static::APPLICATION == $options['application'];
    }

    /**
     * Return a new instance of the adapter by $options
     *
     * @param   array   $options
     * @return  AdapterInterface
     */
    public static function factory( array $options = null )
    {
        return new static( null === $options ? array() : $options );
    }

    /**
     * @return  string
     */
    final public function getApplication()
    {
        return static::APPLICATION;
    }

    /**
     * @return  string
     */
    final public function setApplication( $application )
    {
        if ( static::APPLICATION !== $application )
        {
            throw new Exception\LogicException( sprintf(
                '%s: $application (%s) does not match with "%s"',
                __METHOD__,
                $application,
                static::APPLICATION
            ) );
        }

        return $this;
    }

    /**
     * @return  Structure
     */
    protected function getSpecificStructure()
    {
        if ( null === $this->specificStructure )
        {
            $this->specificStructure = $this->getModel()
                                    ->find( static::APPLICATION );
        }

        return $this->specificStructure;
    }

    /**
     * @return  Structure
     */
    protected function getFallbackStructure()
    {
        if ( null === $this->fallbackStructure )
        {
            if ( static::APPLICATION == static::FALLBACK )
            {
                $this->fallbackStructure = $this->getSpecificStructure();
            }
            else
            {
                $this->fallbackStructure = $this->getModel()
                                                ->find( static::FALLBACK );
            }
        }

        return $this->fallbackStructure;
    }

    /**
     * @return  string
     */
    public function getMode()
    {
        return $this->getSpecificStructure()
                    ->getSetting( 'mode', static::MODE_DEFAULT );
    }

    /**
     * @return  bool
     */
    protected function isModeDefault()
    {
        return static::MODE_DEFAULT == $this->getMode();
    }

    /**
     * Get application's settings
     * as key => value pairs
     *
     * @return  array
     */
    public function getSettings()
    {
        $settings = $this->getSpecificStructure()->settings;

        if ( $this->isModeDefault() )
        {
            $fallback = $this->getFallbackStructure()->settings;

            foreach ( $this->getDefaultSettingsKeys() as $key )
            {
                unset( $settings[$key] );

                if ( isset( $fallback[$key] ) )
                {
                    $settings[$key] = $fallback[$key];
                }
            }
        }

        return $settings;
    }

    /**
     * Has an application's setting available?
     *
     * @param   string  $name
     * @return  bool
     */
    public function hasSetting( $name )
    {
        $structure = $this->isModeDefault()
                  && in_array( $name, $this->getDefaultSettingsKeys() )
                        ? $this->getFallbackStructure()
                        : $this->getSpecificStructure();

        return $structure->hasSetting( $name );
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
        $structure = $this->isModeDefault()
                  && in_array( $name, $this->getDefaultSettingsKeys() )
                        ? $this->getFallbackStructure()
                        : $this->getSpecificStructure();

        return $structure->getSetting( $name, $default );
    }

}
