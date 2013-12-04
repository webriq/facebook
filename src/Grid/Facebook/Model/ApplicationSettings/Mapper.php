<?php

namespace Grid\Facebook\Model\ApplicationSettings;

use Zork\Db\Sql\Sql;
use Zork\Db\Sql\Predicate\NotIn;
use Zend\Stdlib\ArrayUtils;
use Zork\Stdlib\OptionsTrait;
use Zork\Model\Exception;
use Zork\Model\DbAdapterAwareTrait;
use Zork\Model\DbAdapterAwareInterface;
use Zork\Model\MapperAwareInterface;
use Zork\Model\Mapper\ReadOnlyMapperInterface;
use Zork\Model\Mapper\ReadWriteMapperInterface;
use Zork\Model\Mapper\DbAware\DbSchemaAwareTrait;
use Zork\Model\Mapper\DbAware\DbSchemaAwareInterface;

/**
 * Core_Model_Mapper_ReadOnlyAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper implements DbAdapterAwareInterface,
                        DbSchemaAwareInterface,
                        ReadOnlyMapperInterface,
                        ReadWriteMapperInterface
{

    use OptionsTrait,
        DbAdapterAwareTrait,
        DbSchemaAwareTrait;

    /**
     * @var string
     */
    const DEFAULT_TABLE = '*';

    /**
     * Table name used in all queries
     *
     * @var string
     */
    protected static $tableName = 'facebook_application_settings';

    /**
     * @var Structure
     */
    protected $structurePrototype;

    /**
     * Get the table-name
     *
     * @return  string
     */
    protected function getTableName()
    {
        if ( empty( static::$tableName ) )
        {
            throw new Exception\LogicException(
                '$tableName not implemented'
            );
        }

        return $this->getTableInSchema( static::$tableName );
    }

    /**
     * @return  Structure
     */
    public function getStructurePrototype()
    {
        return $this->structurePrototype;
    }

    /**
     * @param   Structure   $structurePrototype
     * @return  Mapper
     */
    public function setStructurePrototype( Structure $structurePrototype = null )
    {
        if ( $structurePrototype instanceof MapperAwareInterface )
        {
            $structurePrototype->setMapper( $this );
        }

        $this->structurePrototype = $structurePrototype;
        return $this;
    }

    /**
     * Constructor
     *
     * @param   Structure   $facebookApplicationSettingsStructurePrototype
     */
    public function __construct( Structure $facebookApplicationSettingsStructurePrototype = null )
    {
        $this->setStructurePrototype( $facebookApplicationSettingsStructurePrototype ?: new Structure );
    }

    /**
     * Create structure from plain data
     *
     * @param   array $data
     * @return  Structure
     */
    protected function createStructure( array $data )
    {
        $structure = clone $this->structurePrototype;

        if ( $structure instanceof MapperAwareInterface )
        {
            $structure->setMapper( $this );
        }

        return $structure->setOptions( $data );
    }

    /**
     * Sql-object
     *
     * @var \Zend\Db\Sql\Sql
     */
    private $sql;

    /**
     * Get a Zend\Db\Sql\Sql object
     *
     * @param   null|string|TableIdentifier $table default: self::DEFAULT_TABLE
     * @return  \Zend\Db\Sql\Sql
     */
    protected function sql( $table = self::DEFAULT_TABLE )
    {
        if ( self::DEFAULT_TABLE === $table )
        {
            if ( null === $this->sql )
            {
                $this->sql = new Sql(
                    $this->getDbAdapter(),
                    $this->getTableName()
                );
            }

            return $this->sql;
        }

        return new Sql(
            $this->getDbAdapter(),
            $table
        );
    }

    /**
     * Find a structure
     *
     * @param   string|array $primaryKeys $application or array( $application )
     * @return  Structure
     */
    public function find( $primaryKeys )
    {
        $primaryKeys = is_array( $primaryKeys )
                     ? $primaryKeys : func_get_args();

        if ( count( $primaryKeys ) < 1 )
        {
            throw new Exception\LogicException(
                'application name is required to find a facebbook application-settings structure'
            );
        }

        list( $application ) = $primaryKeys;

        $data = array(
            'application'   => (string) $application,
            'settings'      => array(),
        );

        $select = $this->sql()
                       ->select()
                       ->columns( array(
                           'key', 'value'
                       ) )
                       ->where( array(
                           'application' => $application,
                       ) );

        $result = $this->sql()
                       ->prepareStatementForSqlObject( $select )
                       ->execute();

        foreach ( $result as $row )
        {
            $data['settings'][$row['key']] = $row['value'];
        }

        return $this->createStructure( $data );
    }

    /**
     * Create structure from plain data
     *
     * @param   array|\Traversable $data
     * @return  Structure
     */
    public function create( $data )
    {
        $data = ArrayUtils::iteratorToArray( $data );
        return $this->createStructure( $data );
    }

    /**
     * Save a structure
     *
     * @param   array|Structure $structure
     * @return  int
     */
    public function save( & $structure )
    {
        if ( $structure instanceof Structure )
        {
            $application    = $structure->application;
            $settings       = $structure->settings;
        }
        else
        {
            $data = (array) $structure;

            if ( ArrayUtils::isHashTable( $data ) )
            {
                $application    = $data['application'];
                $settings       = $data['settings'];
            }
            else
            {
                list( $application,
                      $settings ) = $data;
            }
        }

        $rows       = 0;
        $savedKeys  = array();
        $sql        = $this->sql();

        foreach ( $settings as $key => $value )
        {
            $update = $sql->update()
                          ->set( array(
                              'value' => $value
                          ) )
                          ->where( array(
                              'application' => $application,
                              'key'         => $key,
                          ) );

            $affected = $this->sql()
                             ->prepareStatementForSqlObject( $update )
                             ->execute()
                             ->getAffectedRows();

            if ( $affected )
            {
                $rows += $affected;
            }
            else
            {
                $insert = $sql->insert()
                              ->values( array(
                                  'application' => $application,
                                  'key'         => $key,
                                  'value'       => $value,
                              ) );

                $rows += $this->sql()
                              ->prepareStatementForSqlObject( $insert )
                              ->execute()
                              ->getAffectedRows();
            }

            $savedKeys[] = $key;
        }

        $deleteWhere = array(
            'application'   => $application,
        );

        if ( ! empty( $savedKeys ) )
        {
            $deleteWhere[] = new NotIn( 'key', $savedKeys );
        }

        $delete = $this->sql()
                       ->delete()
                       ->where( $deleteWhere );

        $rows += $this->sql()
                      ->prepareStatementForSqlObject( $delete )
                      ->execute()
                      ->getAffectedRows();

        return $rows;
    }

    /**
     * Remove a structure
     *
     * @param   string|array|Structure $structureOrPrimaryKeys
     * @return  int
     */
    public function delete( $structureOrPrimaryKeys )
    {
        if ( is_array( $structureOrPrimaryKeys ) )
        {
            if ( isset( $structureOrPrimaryKeys['application'] ) &&
                 isset( $structureOrPrimaryKeys['settings'] ) )
            {
                $application = $structureOrPrimaryKeys['application'];
            }
            else
            {
                list( $application ) = $structureOrPrimaryKeys;
            }
        }
        else
        {
            $application = $structureOrPrimaryKeys->application;
        }

        $delete = $this->sql()
                       ->delete()
                       ->where( array(
                           'application' => $application,
                       ) );

        $result = $this->sql()
                       ->prepareStatementForSqlObject( $delete )
                       ->execute();

        return $result->getAffectedRows();
    }

}
