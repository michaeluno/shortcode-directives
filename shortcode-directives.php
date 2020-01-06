<?php
/**
 * Plugin Name:    Shortcode Directives
 * Plugin URI:     http://en.michaeluno.jp/shortcode-directivies
 * Description:    Enables shortcode directives that perform certain actions on posts and comments.
 * Author:         Michael Uno
 * Author URI:     http://en.michaeluno.jp
 * Version:        1.0.0
 * Text Domain:   
 * Domain Path:    language
 */

/**
 * Provides the basic information about the plugin.
 * 
 * @since    0.0.1       
 */
class ShortcodeDirectives_Registry_Base {
 
    const VERSION        = '1.0.0';    // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
    const NAME           = 'Shortcode Directives';
    const DESCRIPTION    = 'Enables shortcode directives that perform certain actions on posts and comments.';
    const URI            = 'http://en.michaeluno.jp/shortcode-directivies';
    const AUTHOR         = 'Michael Uno';
    const AUTHOR_URI     = 'http://en.michaeluno.jp';
    const PLUGIN_URI     = 'http://en.michaeluno.jp/shortcode-directivies';
    const COPYRIGHT      = 'Copyright (c) 2020, Michael Uno';
    const LICENSE        = 'GPL v2 or later';
    const CONTRIBUTORS   = '';
 
}

/**
 * Provides the common data shared among plugin files.
 * 
 * To use the class, first call the setUp() method, which sets up the necessary properties.
 * 
 * @package     Shortcode Directives
 * @since       0.0.1
*/
final class ShortcodeDirectives_Registry extends ShortcodeDirectives_Registry_Base {
    
    const TEXT_DOMAIN               = 'shortcode-directives';
    const TEXT_DOMAIN_PATH          = '/language';
    
    /**
     * The hook slug used for the prefix of action and filter hook names.
     * 
     * @remark      The ending underscore is not necessary.
     */    
    const HOOK_SLUG                 = 'scd';    // without trailing underscore
    
    /**
     * The transient prefix. 
     * 
     * @remark      This is also accessed from uninstall.php so do not remove.
     * @remark      Up to 8 characters as transient name allows 45 characters or less ( 40 for site transients ) so that md5 (32 characters) can be added
     */    
    const TRANSIENT_PREFIX          = 'SCD';
    
    /**
     * 
     * @since       0.0.1
     */
    static public $sFilePath = __FILE__;
    
    /**
     * 
     * @since       0.0.1
     */    
    static public $sDirPath;    

    /**
     * rtrim( sys_get_temp_dir(), '/' ) . '/' . ShortcodeDirectives_Registry::$sTempDirBaseName;
     * @since   0.0.5
     * @var string
     */
    static public $sTempDirBaseName = '';

    /**
     * @since    0.0.1
     */
    static public $aOptionKeys = array(    
        'setting'           => 'shortcode_directives',
        'log'               => 'shortcode_directives_log',
    );

    /**
     * Represents the plugin options structure and their default values.
     * @var         array
     * @since       0.0.3
     */
    static public $aOptions = array(
        'delete'    => array(
            'delete_on_uninstall' => false,
        ),

        'log'       => array(
            'keep' => array(
                'enable' => true,
                'length' => 100,
            ),
        ),
        'post_types' => array(
            'post'  => array(
                'areas'    => array(
                    'post'    => true,
                    'comment' => true,
                ),
                'permissions'   => array(
                    'user_roles' => array(
                        'administrator' => true,
                        'editor'        => true,
                    ),
                    'author'    => true,
                ),
            ),
            'page'  => array(
                'areas'    => array(
                    'post'    => true,
                    'comment' => true,
                ),
                'permissions'   => array(
                    'user_roles' => array(
                        'administrator' => true,
                        'editor'        => true,
                    ),
                    'author'    => true,
                ),
            ),
        ),

    );
        
    /**
     * Used admin pages.
     * @since    0.0.1
     */
    static public $aAdminPages = array(
        // key => 'page slug'        
        'setting'           => 'scd_settings',
    );
    
    /**
     * Used post types.
     */
    static public $aPostTypes = array(
    );
    
    /**
     * Used post types by meta boxes.
     */
    static public $aMetaBoxPostTypes = array(
    );
    
    /**
     * Used taxonomies.
     * @remark      
     */
    static public $aTaxonomies = array(
    );

    /**
     * Used user meta keys.
     * @var array
     */
    static public $aUserMetas = array(
        // meta key => ...whatever values for notes
    );

    static public $aPostMetas = array(
        // meta key => ...whatever values for notes
    );

    /**
     * Used shortcode slugs
     */
    static public $aShortcodes = array(
    );

    /**
     * Stores custom database table names.
     * @remark      The below is the structure
     * array(
     *      'slug (part of database wrapper class file name)' => array(
     *          'version'   => '0.1',
     *          'name'      => 'table_name',    // serves as the table name suffix
     *      ),
     *      ...
     * )
     * @since       0.0.3
     */
    static public $aDatabaseTables = array(
        // 'ft_tweets'        => array(
        // 'name'              => 'ft_tweets', // serves as the table name suffix
        // 'version'           => '0.0.1',
        // 'across_network'    => true,
        // 'class_name'        => 'ShortcodeDirectives_DatabaseTable_ft_tweets',
        // ),
//        'ft_http_requests' => array(
//            'name'              => 'ft_http_requests',  // serves as the table name suffix
//            'version'           => '0.0.1',
//            'across_network'    => true,
//            'class_name'        => 'ShortcodeDirectives_DatabaseTable_ft_http_requests',
//        ),
    );

    /**
     * Stores action hook names registered with WP Cron.
     * @var array
     */
    static public $aScheduledActionHooks = array(
        // key (whatever) => value: the name of the action hook
    );

    /**
     * Stores custom keys for the WP Cron intervals.
     * @var array
     */
    static public $aWPCronIntervals = array(
    );

    static public $aCookieSlugs = array(
        // (any) => cookie slug (cookie slug where in $_COOKIE[ slug ])
    );


    /**
     * Sets up class properties.
     * @return      void
     */
    static function setUp() {
        self::$sDirPath  = dirname( self::$sFilePath );  
    }    
    
    /**
     * @return      string
     */
    public static function getPluginURL( $sPath='', $bAbsolute=false ) {
        $_sRelativePath = $bAbsolute
            ? str_replace('\\', '/', str_replace( self::$sDirPath, '', $sPath ) )
            : $sPath;
        if ( isset( self::$_sPluginURLCache ) ) {
            return self::$_sPluginURLCache . $_sRelativePath;
        }
        self::$_sPluginURLCache = trailingslashit( plugins_url( '', self::$sFilePath ) );
        return self::$_sPluginURLCache . $_sRelativePath;
    }
        /**
         * @since    0.0.1
         */
        static private $_sPluginURLCache;

    /**
     * Requirements.
     * @since    0.0.1
     */    
    static public $aRequirements = array(
        'php' => array(
            'version'   => '5.2.4',
            'error'     => 'The plugin requires the PHP version %1$s or higher.',
        ),
        'wordpress'         => array(
            'version'   => '4.5',
            'error'     => 'The plugin requires the WordPress version %1$s or higher.',
        ),
        // 'mysql'             => array(
            // 'version'   => '5.0.3', // uses VARCHAR(2083) 
            // 'error'     => 'The plugin requires the MySQL version %1$s or higher.',
        // ),
        'functions'     => '', // disabled
        // array(
            // e.g. 'mblang' => 'The plugin requires the mbstring extension.',
        // ),
        // 'classes'       => array(
            // 'DOMDocument' => 'The plugin requires the DOMXML extension.',
        // ),
        'constants'     => '', // disabled
        // array(
            // e.g. 'THEADDONFILE' => 'The plugin requires the ... addon to be installed.',
            // e.g. 'APSPATH' => 'The script cannot be loaded directly.',
        // ),
        'files'         => '', // disabled
        // array(
            // e.g. 'home/my_user_name/my_dir/scripts/my_scripts.php' => 'The required script could not be found.',
        // ),
    );

    static public function setAdminNotice( $sMessage, $sType ) {
        self::$aAdminNotices[] = array( 'message' => $sMessage, 'type' => $sType );
        add_action( 'admin_notices', array( __CLASS__, 'replyToShowAdminNotices' ) );
    }
        static public $aAdminNotices = array();
        static public function replyToShowAdminNotices() {
            foreach( self::$aAdminNotices as $_aNotice ) {
                $_sType = esc_attr( $_aNotice[ 'type' ] );
                echo "<div class='{$_sType}'>"
                     . "<p>" . $_aNotice[ 'message' ] . "</p>"
                     . "</div>";
            }
        }

    static public function registerClasses( array $aClasses ) {
        self::$___aAutoLoadClasses = $aClasses + self::$___aAutoLoadClasses;
        spl_autoload_register( array( __CLASS__, 'replyToLoadClass' ) );
    }
        static private $___aAutoLoadClasses = array();
        static public function replyToLoadClass( $sCalledUnknownClassName ) {
            if ( ! isset( self::$___aAutoLoadClasses[ $sCalledUnknownClassName ] ) ) {
                return;
            }
            include( self::$___aAutoLoadClasses[ $sCalledUnknownClassName ] );
        }

}
ShortcodeDirectives_Registry::setUp();

// Do not load if accessed directly. Not exiting here because other scripts will load this main file such as uninstall.php and inclusion list generator
// and if it exists their scripts will not complete.
if ( ! defined( 'ABSPATH' ) ) {
    return;
}

if ( defined( 'DOING_TESTS' ) && DOING_TESTS ) {
    return;
}

include( dirname( __FILE__ ).'/include/library/apf/admin-page-framework.php' );
include( dirname(__FILE__ ) . '/include/ShortcodeDirectives_Bootstrap.php');
new ShortcodeDirectives_Bootstrap(
    ShortcodeDirectives_Registry::$sFilePath,
    ShortcodeDirectives_Registry::HOOK_SLUG    // hook prefix
);