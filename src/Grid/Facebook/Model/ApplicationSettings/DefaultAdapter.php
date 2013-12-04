<?php

namespace Grid\Facebook\Model\ApplicationSettings;

/**
 * DefaultAdapter
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DefaultAdapter extends AbstractAdapter
{

    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     *
     * @param   array   $options;
     * @return  float
     */
    public static function acceptsOptions( array $options )
    {
        return 0.01;
    }

    /**
     * Does not include any of the default keys
     *
     * @return  array
     */
    public static function getDefaultSettingsKeys()
    {
        return array();
    }

}
