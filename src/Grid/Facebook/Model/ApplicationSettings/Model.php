<?php

namespace Grid\Facebook\Model\ApplicationSettings;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * Construct model
     *
     * @param   Mapper  $facebbookApplicationSettingsMapper
     */
    public function __construct( Mapper $facebbookApplicationSettingsMapper )
    {
        $this->setMapper( $facebbookApplicationSettingsMapper );
    }

    /**
     * Find an application's settings by its name
     *
     * @param   string  $application
     * @return  Structure
     */
    public function find( $application )
    {
        return $this->getMapper()
                    ->find( $application );
    }

}
