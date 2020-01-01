<?php
$GLOBALS[ '_sProjectDirPath' ]    = dirname( codecept_root_dir() );
$GLOBALS[ '_sTestSiteDirPath' ]   = dirname( dirname( dirname( $GLOBALS['_sProjectDirPath'] ) ) );

// ABSPATH is needed to load the framework.
define( 'ABSPATH', $GLOBALS[ '_sTestSiteDirPath' ]. '/' );


codecept_debug( 'Unit: _bootstrap.php loaded' );
var_dump( 'unit bootstrap' );

define( 'DOING_TESTS', true );

// Paths
$_sPluginRootDirPath = dirname( codecept_root_dir() );
include( $_sPluginRootDirPath . '/shortcode-directives.php' );
include( $_sPluginRootDirPath . '/include/class-list.php' );
ShortcodeDirectives_Registry::registerClasses( $_aClassFiles );