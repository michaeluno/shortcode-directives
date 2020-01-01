<?php
/**
 * Cleans up the plugin options.
 *    
 * @package      Shortcode Directives
 * @copyright    Copyright (c) 2020, <Michael Uno>
 * @author       Michael Uno
 * @authorurl    http://en.michaeluno.jp
 * @since        0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/* 
 * Plugin specific constant. 
 * We are going to load the main file to get the registry class. And in the main file, 
 * if this constant is set, it will return after declaring the registry class.
 **/
if ( ! defined( 'DOING_PLUGIN_UNINSTALL' ) ) {
    define( 'DOING_PLUGIN_UNINSTALL', true  );
}

/**
 * Set the main plugin file name here.
 */
$_sMainPluginFileName  = 'shortcode-directives.php';
if ( file_exists( dirname( __FILE__ ). '/' . $_sMainPluginFileName ) ) {
   include( $_sMainPluginFileName );
}

if ( ! class_exists( 'ShortcodeDirectives_Registry' ) ) {
    return;
}

// 0. Delete the temporary directory
$_sTempDirPath      = rtrim( sys_get_temp_dir(), '/' ) . '/' . ShortcodeDirectives_Registry::$sTempDirBaseName;
$_sTempDirPath_Site = $_sTempDirPath . '/' . md5( site_url() );
if ( file_exists( $_sTempDirPath_Site ) && is_dir( $_sTempDirPath_Site ) ) {
    ShortcodeDirectives_Utility::removeDirectoryRecursive( $_sTempDirPath_Site );
}
/// Consider other sites on the same server uses the plugin
if ( is_dir( $_sTempDirPath ) && ShortcodeDirectives_Utility::isDirectoryEmpty( $_sTempDirPath ) ) {
    ShortcodeDirectives_Utility::removeDirectoryRecursive( $_sTempDirPath );
}

// 1. Delete transients
$_aPrefixes = array(
    ShortcodeDirectives_Registry::TRANSIENT_PREFIX, // the plugin transients
    'apf_',      // the admin page framework transients
);
foreach( $_aPrefixes as $_sPrefix ) {
    if ( ! $_sPrefix ) { 
        continue; 
    }
    $GLOBALS[ 'wpdb' ]->query( "DELETE FROM `" . $GLOBALS[ 'table_prefix' ] . "options` WHERE `option_name` LIKE ( '_transient_%{$_sPrefix}%' )" );
    $GLOBALS[ 'wpdb' ]->query( "DELETE FROM `" . $GLOBALS[ 'table_prefix' ] . "options` WHERE `option_name` LIKE ( '_transient_timeout_%{$_sPrefix}%' )" );
}

// 2. Delete plugin data
$_oOption  = ShortcodeDirectives_Option::getInstance();
if ( ! $_oOption->get( array( 'delete', 'delete_upon_uninstall' ) ) ) {
    return true;
}

// Options stored in the `options` table.
array_walk_recursive( 
    ShortcodeDirectives_Registry::$aOptionKeys, // subject array
    'delete_option'   // function name
);

// Delete custom tables
foreach( ShortcodeDirectives_Registry::$aDatabaseTables as $_aTable ) {
    if ( ! class_exists( $_aTable[ 'class_name' ] ) ) {
        continue;
    }
    $_oTable  = new $_aTable[ 'class_name' ];
    if ( ! method_exists( $_oTable, 'uninstall' ) ) {
        continue;
    }
    $_oTable->uninstall();
}

// Remove user meta keys used by the plugin
foreach( Zapper_Registry::$aUserMetas as $_sMetaKey => $_v ) {
    delete_metadata(
        'user',    // the user meta type
        0,  // does not matter here as deleting them all
        $_sMetaKey,
        '', // does not matter as deleting
        true // whether to delete all
    );
}