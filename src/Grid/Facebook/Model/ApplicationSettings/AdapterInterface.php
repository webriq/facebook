<?php

namespace Grid\Facebook\Model\ApplicationSettings;

use Zork\Model\ModelAwareInterface;
use Zork\Factory\AdapterInterface as FactoryAdapterInterface;

/**
 * AdapterInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface AdapterInterface extends FactoryAdapterInterface,
                                   ModelAwareInterface
{

    /**
     * Get application's settings
     * as key => value pairs
     *
     * @return  array
     */
    public function getSettings();

    /**
     * Has an application's setting available?
     *
     * @param   string  $name
     * @return  bool
     */
    public function hasSetting( $name );

    /**
     * Get an application's setting
     *
     * @param   string  $name
     * @param   mixed   $default
     * @return  bool
     */
    public function getSetting( $name, $default = null );

    /**
     * Get default settings' keys
     *
     * @return  array
     */
    public static function getDefaultSettingsKeys();

}
