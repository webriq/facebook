-- insert default values for table: module

INSERT INTO "module" ( "module", "enabled" )
     VALUES ( 'Grid\Facebook', TRUE );

-- insert default values for table: user_right

DO LANGUAGE plpgsql $$
BEGIN

    IF NOT EXISTS ( SELECT *
                      FROM "user_right"
                     WHERE "group"      = 'settings'
                       AND "resource"   = 'settings.facebook'
                       AND "privilege"  = 'edit' ) THEN

    ELSE

        INSERT INTO "user_right" ( "label", "group", "resource", "privilege", "optional", "module" )
             VALUES ( NULL, 'settings', 'settings.facebook', 'edit', TRUE, '' );

    END IF;

    IF EXISTS ( SELECT *
                  FROM "settings"
                 WHERE "key" IN ( 'modules.Grid\FacebookLogin.appId',
                                  'modules.Grid\FacebookLogin.appSecret',
                                  'modules.Grid\User.features.loginWith.Facebook.enabled' )
                   AND "type" = 'ini' ) THEN

        INSERT INTO "facebook_application_settings" ( "application", "key", "value" )
             VALUES ( 'login', 'mode', 'specific' ),
                    ( 'login', 'enabled', COALESCE( ( SELECT "value"
                                                        FROM "settings"
                                                       WHERE "key"  = 'modules.Grid\User.features.loginWith.Facebook.enabled'
                                                         AND "type" = 'ini'
                                                       LIMIT 1 ), '' ) ),
                    ( 'login', 'appId', COALESCE( ( SELECT "value"
                                                      FROM "settings"
                                                     WHERE "key"    = 'modules.Grid\FacebookLogin.appId'
                                                       AND "type"   = 'ini'
                                                     LIMIT 1 ), '' ) ),
                    ( 'login', 'appSecret', COALESCE( ( SELECT "value"
                                                          FROM "settings"
                                                         WHERE "key"    = 'modules.Grid\FacebookLogin.appSecret'
                                                           AND "type"   = 'ini'
                                                         LIMIT 1 ), '' ) );

        DELETE FROM "settings"
              WHERE "key" IN ( 'modules.Grid\FacebookLogin.appId',
                               'modules.Grid\FacebookLogin.appSecret',
                               'modules.Grid\User.features.loginWith.Facebook.enabled' )
                AND "type" = 'ini';

    END IF;

END $$;
