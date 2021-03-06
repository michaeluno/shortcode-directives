<?php
/**
 * Minifies PHP files into a single file.
 *
 */

/* Set necessary paths */
$sTargetBaseDir		= dirname( dirname( dirname( __FILE__ ) ) );
$sTargetDir			= $sTargetBaseDir . '/asset/js/';

/* If accessed from a browser, exit. */
$bIsCLI				= php_sapi_name() == 'cli';
$sCarriageReturn	= $bIsCLI ? PHP_EOL : '<br />';
if ( ! $bIsCLI ) { 
    exit( 'Please run the script with a console program.' );
}

/* Include necessary files */
require( dirname( __FILE__ ) . '/class/JS_Files_Minifier.php' );

/* Create a minified version of the framework. */
echo 'Started...' . $sCarriageReturn;
new JS_Files_Minifier( 
	$sTargetDir, 
	'',     // the same directory to the target file.
	array(
		'header_class_name'	=>	'ShortcodeDirectives_Registry_Base',
		'header_class_path'	=>	$sTargetBaseDir . '/shortcode-directives.php',
		'output_buffer'		=>	true,
		'header_type'		=>	'CONSTANTS',	
		// 'exclude_classes'	=>	array(
			// 'AdminPageFramework_InclusionClassFilesHeader',
			// 'admin-page-framework-include-class-list',
		// ),
		'search'			=>	array(
			'allowed_extensions'	=>	array( 'js' ),	// e.g. array( 'php', 'inc' )
			'exclude_extensions'	=>	array( 'min.js' ),
			// 'exclude_dir_paths'		=>	array( $sTargetBaseDir . '/include/class/admin' ),
			// 'exclude_dir_names'		=>	array( '_document', 'document' ),
			'is_recursive'			=>	true,
		),			        
	)
);

echo 'Done!' . $sCarriageReturn;