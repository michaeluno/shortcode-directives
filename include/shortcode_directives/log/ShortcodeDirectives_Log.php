<?php
/**
 */


/**
 */
class ShortcodeDirectives_Log extends ShortcodeDirectives_PluginUtility {

    /**
     * Performs necessary set-ups.
     */
    public function __construct() {
        add_action( 'shortcode_directives_action_did_directive', array( $this, 'replyToLog' ), 10, 3 );

    }

    public function replyToLog( $mResult, $sTagName, $aAttributes ) {

        $_sOptionKey  = ShortcodeDirectives_Registry::$aOptionKeys[ 'log' ];
        $_aLog        = get_option( $_sOptionKey, array() );
        $_sTime       = current_time( "Y/m/d H:i:s", false );
        $_sAttributes = $this->___getReadableAttributes( $aAttributes );
        $_sResult     = $this->___getReadableResult( $mResult );
        $_aLog[]      = $_sTime . ' ' . $sTagName . ' ' . $_sAttributes . ' ' . $_sResult;
        $_aLog        = array_slice( $_aLog, 0, 100 );
        update_option( $_sOptionKey, $_aLog );

    }
        private function ___getReadableResult( $mResult ) {

            $_sResult = '';
            if ( ! is_scalar( $mResult ) ) {
                foreach( $mResult as $_isIndex => $asValue ) {
                    if ( is_wp_error( $asValue ) ) {
                        $_sResult .= $asValue->get_error_code() . ' ' . implode( '; ', $asValue->get_error_messages() );
                        continue;
                    }
                    $_sResult .= is_array( $asValue )
                        ? implode( ', ', $asValue )
                        : $asValue;
                }
                return $_sResult;
            }

            $_sType = "(" . gettype( $mResult ) . ")";
            if ( is_bool( $mResult ) ) {
                return $_sType. " " . ( $mResult ? 'true' : 'false' );
            }
            return $_sType. " " . ( string ) $mResult;

        }
        private function ___getReadableAttributes( array $aAttributes ) {
            $_sAttribute = '';
            foreach( $aAttributes as $_sAttribute => $_sValue ) {
                $_sValue = ( string ) $_sValue;
                $_sAttribute .= strlen( $_sValue )
                    ? "{$_sAttribute}={$_sValue} "
                    : "{$_sAttribute} ";
            }
            return rtrim( $_sAttribute );
        }

}