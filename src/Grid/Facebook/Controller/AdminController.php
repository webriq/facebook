<?php

namespace Grid\Facebook\Controller;

use Zork\Stdlib\Message;
use Zork\Mvc\Controller\AbstractAdminController;

/**
 * AdminController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdminController extends AbstractAdminController
{

    /**
     * @var array
     */
    protected $aclRights = array(
        'application-settings'  => array(
            'settings.facebook' => 'edit',
        ),
    );

    /**
     * Edit facebook application(s)' settings
     */
    public function applicationSettingsAction()
    {
        /* @var $form \Zork\Form\Form */
        /* @var $model \Grid\Facebook\Model\ApplicationSettings\Model */
        $request        = $this->getRequest();
        $serviceLocator = $this->getServiceLocator();
        $model          = $serviceLocator->get( 'Grid\Facebook\Model\ApplicationSettings\Model' );
        $form           = $serviceLocator->get( 'Form' )
                                         ->get( 'Grid\User\Group' );

        $data       = array();
        $structures = array();

        foreach ( $form->getFieldsets() as $fieldset )
        {
            /* @var $fieldset \Zend\Form\Fieldset */
            $name               = $fieldset->getName();
            $structures[$name]  = $model->find( $name );

            if ( empty( $structures[$name] ) )
            {
                unset( $structures[$name] );
            }
            else
            {
                $data[$name] = $structures[$name]->settings;
            }
        }

        if ( empty( $structures ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        $form->setData( $data );

        if ( $request->isPost() )
        {
            $form->setData( $request->getPost() );
            $saved = 0;

            if ( $form->isValid() )
            {
                $data = $form->getData();

                foreach ( $structures as $name => $structure )
                {
                    if ( isset( $data[$name] ) )
                    {
                        $structure->settings = $data[$name];
                        $saved += $structure->save();
                    }
                }
            }

            if ( $saved > 0 )
            {
                $this->messenger()
                     ->add( 'facebook.form.settings.success',
                            'facebook', Message::LEVEL_INFO );
            }
            else
            {
                $this->messenger()
                     ->add( 'facebook.form.settings.failed',
                            'facebook', Message::LEVEL_ERROR );
            }
        }

        return array(
            'form' => $form,
        );
    }

}
