--------------------------------------------------------------------------------
-- table: facebook_application_settings                                       --
--------------------------------------------------------------------------------

CREATE TABLE "facebook_application_settings"
(
    "id"            SERIAL              NOT NULL,
    "application"   CHARACTER VARYING   NOT NULL    DEFAULT 'default',
    "key"           CHARACTER VARYING   NOT NULL,
    "value"         CHARACTER VARYING   NOT NULL,

    PRIMARY KEY ( "id" ),
    UNIQUE ( "application", "key" )
);
