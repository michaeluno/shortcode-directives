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
        add_action( 'shortcode_directives_action_did_directive', array( $this, 'replyToLogForDirectives' ), 10, 3 );
        add_action( 'shortcode_directives_action_saved_post', array( $this, 'replyToLogForSavingPost' ), 10 );
    }

    public function replyToLogForSavingPost( $mResult ) {
        $_oOption     = ShortcodeDirectives_Option::getInstance();
        $_bKeepLog    = $_oOption->get( array( 'log', 'keep', 'enable' ), true );
        if ( ! $_bKeepLog ) {
            return;
        }
        $_iKeepLength = $_oOption->get( array( 'log', 'keep', 'length' ), true );
        $_sOptionKey  = ShortcodeDirectives_Registry::$aOptionKeys[ 'log' ];
        $_aLog        = get_option( $_sOptionKey, array() );
        $_sTime       = current_time( "Y/m/d H:i:s", false );
        $_sResult     = $this->___getReadableResult( $mResult );
        $_oUser       = wp_get_current_user();
        $_aLog[]      = sprintf(
            '%1$s "%2$s" On saving post: %3$s',
            $_sTime, $_oUser->display_name, $_sResult
        );
        $_iOffset     = max( count( $_aLog ) - $_iKeepLength, 0 );
        $_aLog        = array_slice( $_aLog, $_iOffset, $_iKeepLength );
        update_option( $_sOptionKey, $_aLog );

    }
    public function replyToLogForDirectives( $mResult, $sTagName, $sAttributes ) {

        $_oOption     = ShortcodeDirectives_Option::getInstance();
        $_bKeepLog    = $_oOption->get( array( 'log', 'keep', 'enable' ), true );
        if ( ! $_bKeepLog ) {
            return;
        }
        $_iKeepLength = $_oOption->get( array( 'log', 'keep', 'length' ), true );
        $_sOptionKey  = ShortcodeDirectives_Registry::$aOptionKeys[ 'log' ];
        $_aLog        = get_option( $_sOptionKey, array() );
        $_sTime       = current_time( "Y/m/d H:i:s", false );
        $_sResult     = $this->___getReadableResult( $mResult );
        $_oUser       = wp_get_current_user();
        $_aLog[]      = sprintf(
            '%1$s "%2$s" [%3$s %4$s] %5$s',
            $_sTime, $_oUser->display_name, $sTagName, trim( $sAttributes ), $_sResult
        );
        $_iOffset     = max( count( $_aLog ) - $_iKeepLength, 0 );
        $_aLog        = array_slice( $_aLog, $_iOffset, $_iKeepLength );
        update_option( $_sOptionKey, $_aLog );

    }
        private function ___getReadableResult( $mResult ) {

            if ( is_wp_error( $mResult ) ) {
                return $this->getWPErrorMessages( $mResult );
            }

            $_sResult = '';
            // Case: array | object
            if ( is_array( $mResult ) || is_object( $mResult ) ) {
                foreach( $mResult as $_isIndex => $asValue ) {
                    if ( is_wp_error( $asValue ) ) {
                        $_sResult .= $this->getWPErrorMessages( $asValue ) . ' ';
                        continue;
                    }
                    $_sResult .= is_array( $asValue )
                        ? implode( ', ', $asValue ) . ' '
                        : $asValue . ' ';
                }
                return rtrim( $_sResult );
            }
            // Case: string
            if ( is_string( $mResult ) ) {
                return $mResult;
            }

            // Case: other variable types
            $_sType  = "(" . gettype( $mResult ) . ")";
            if ( is_bool( $mResult ) ) {
                return $_sType. " " . ( $mResult ? 'true' : 'false' );
            }
            $mResult = is_null( $mResult ) ? 'NULL' : $mResult;
            return $_sType. " " . ( string ) $mResult;

        }

        /**
         * @param array $aAttributes
         *
         * @return string
         * @deprecated
         */
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