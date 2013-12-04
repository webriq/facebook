/**
 * Facebook jssdk helpers
 * @package zork
 * @subpackage facebook
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
( function ( global, $, js, undefined )
{
    "use strict";

    if ( typeof js.facebook !== "undefined" )
    {
        return;
    }

    /**
     * @class Facebook module
     * @constructor
     * @memberOf Zork
     */
    global.Zork.Facebook = function ()
    {
        this.version = "1.0";
        this.modulePrefix = [ "zork", "facebook" ];
    };

    global.Zork.prototype.facebook = new global.Zork.Facebook();

    var inited  = false,
        initing = false,
        queue   = [],
        href    = "http://connect.facebook.net/%locale%/all.js",
        appId   = null,
        locale  = "en_US",
        fAppId  = function () {
            return appId = $( 'meta[property="fb:app_id"]' ).attr( "content" )
                || appId;
        },
        fLocale = function () {
            return locale = $( 'meta[property="og:locale"]' ).attr( "content" )
                || locale;
        },
        init    = function ( parse, callback ) {
            initing = true;

            $( "body" ).prepend( '<div id="fb-root"></div>' );

            if ( callback )
            {
                queue.push( callback );
            }

            var appId   = fAppId(),
                options = {
                    "status": true,
                    "cookie": true,
                    "xfbml": !! parse
                };

            if ( appId )
            {
                options.appId = appId;
            }

            $.getScript(
                href.replace( /%locale%/g, fLocale() ),
                function() {
                    global.FB.init( options );
                    inited = true;
                    queue.forEach( function ( cb ) {
                        setTimeout( cb, 1 );
                    } );
                }
            );
        };

    /**
     * Initialize facebook jssdk
     *
     * @param   {Boolean}   parse
     * @param   {Function}  callback
     * @returns {Boolean}
     */
    global.Zork.Facebook.prototype.init = function ( parse, callback )
    {
        if ( typeof parse === 'undefined' || null === parse )
        {
            parse = true;
        }

        if ( inited )
        {
            if ( parse )
            {
                js.facebook.xfbml( null, callback );
                return true;
            }
            else
            {
                return false;
            }
        }
        else if ( initing )
        {
            if ( parse )
            {
                queue.push( function () {
                    js.facebook.xfbml( null, callback );
                } );
            }
            else if ( callback )
            {
                queue.push( callback );
            }

            return true;
        }
        else
        {
            init( parse, callback );
            return true;
        }
    };

    /**
     * Parse facebook's xfbml nodes
     *
     * @param   {$|HTMLElement|String|Null} element
     * @param   {Function}                  callback
     * @returns {Boolean}
     */
    global.Zork.Facebook.prototype.xfbml = function ( element, callback )
    {
        if ( typeof element === 'undefined' || null === element )
        {
            element = undefined;
        }
        else
        {
            element = $( element ).parent();

            if ( element && 0 in element )
            {
                element = element[0];
            }
            else
            {
                return;
            }
        }

        var finish = function () {
            global.FB.XFBML.parse(
                element,
                callback ? callback : undefined
            );
        };

        if ( inited )
        {
            finish();
        }
        else if ( initing )
        {
            queue.push( finish );
        }
        else
        {
            js.facebook.init( false, finish );
        }
    };

    global.Zork.Facebook.prototype.xfbml.isElementConstructor = true;

} ( window, jQuery, zork ) );
