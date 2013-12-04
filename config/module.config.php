<?php

return array(
    'router' => array(
        'routes' => array(
            'Grid\Facebook\Admin\ApplicationSettings' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/app/:locale/admin/facebook/application-settings',
                    'constraints'   => array(
                        'locale'    => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grid\Facebook\Controller\Admin',
                        'action'     => 'application-settings',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Grid\Facebook\Controller\Admin' => 'Grid\Facebook\Controller\AdminController',
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            'facebook'     => array(
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../languages/facebook',
                'pattern'       => '%s.php',
                'text_domain'   => 'facebook',
            ),
        ),
    ),
    'factory' => array(
        'Grid\Facebook\Model\ApplicationSettings\AdapterFactory' => array(
            'dependency'    => 'Grid\Facebook\Model\ApplicationSettings\AdapterInterface',
            'adapter'       => array(
                'default'   => 'Grid\Facebook\Model\ApplicationSettings\DefaultAdapter',
            ),
        ),
    ),
    'modules' => array(
        'Grid\Core' => array(
            'navigation'    => array(
                'settings'  => array(
                    'pages' => array(
                        'service'   => array(
                            'label'         => 'admin.navTop.service',
                            'textDomain'    => 'admin',
                            'order'         => 7,
                            'uri'           => '#',
                            'parentOnly'    => true,
                            'pages'         => array(
                                'facebook'  => array(
                                    'label'         => 'admin.navTop.settings.facebook',
                                    'textDomain'    => 'admin',
                                    'order'         => 2,
                                    'route'         => 'Grid\Facebook\Admin\ApplicationSettings',
                                    'resource'      => 'settings.facebook',
                                    'privilege'     => 'edit',
                                    'dependencies'  => array(
                                        'Grid\Facebook\Model\ApplicationSettings\AdapterFactory::haveExtensions' => array(
                                            'service'   => 'Grid\Facebook\Model\ApplicationSettings\AdapterFactory',
                                            'method'    => 'haveExtensions'
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'form' => array(
        'Grid\Facebook\ApplicationSettings' => array(
            'type'          => 'Grid\Facebook\Form\ApplicationSettings',
            'attributes'    => array(
                'data-js-type' => 'js.form.fieldsetTabs',
            ),
            'fieldsets'     => array(
                'default'   => array(
                    'spec'  => array(
                        'name'      => 'default',
                        'options'   => array(
                            'label'       => 'facebook.form.settings.legend',
                            'description' => 'facebook.form.settings.description',
                        ),
                        'elements'  => array(
                            'appId' => array(
                                'spec'  => array(
                                    'type'  => 'Zork\Form\Element\Text',
                                    'name'  => 'appId',
                                    'options'   => array(
                                        'required'  => false,
                                        'label'     => 'facebook.form.settings.appId',
                                    ),
                                ),
                            ),
                            'appSecret' => array(
                                'spec'  => array(
                                    'type'  => 'Zork\Form\Element\Text',
                                    'name'  => 'appSecret',
                                    'options'   => array(
                                        'required'  => false,
                                        'label'     => 'facebook.form.settings.appSecret',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'elements'      => array(
                'submit'    => array(
                    'spec'  => array(
                        'type'  => 'Zork\Form\Element\Submit',
                        'name'  => 'submit',
                        'options'   => array(
                            'required'  => false,
                            'label'     => 'facebook.form.settings.submit',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'grid/facebook/admin/application-settings' => __DIR__ . '/../view/grid/facebook/admin/application-settings.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
