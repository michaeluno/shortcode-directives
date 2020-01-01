<?php

/**
 * Loads the Directive Shortcode component.
 *
 */
class ShortcodeDirectives_Area_Posts extends ShortcodeDirectives_Area_Base {

    protected function _construct() {
        add_filter( 'wp_insert_post_data', array( $this, 'replyToPreprocessPost' ), 10, 2 );
    }

    /**
     * @param array $aData    An array of slashed post data.
     * @param array $aPost    An array of sanitized, but otherwise unmodified post data.
     * @return mixed
     */
    public function replyToPreprocessPost( $aData, $aPost ) {
        if ( ! $this->_isAuthorizedUser( $aData[ 'post_author' ], $aPost ) ) {
            return $aData;
        }
        do_action( 'shortcode_directives_action_register_directives_post', $aData, $aPost );
        $aData[ 'post_content' ] = $this->_processShortcode(
            $aData[ 'post_content' ],
            $this->_getDirectiveNames( 'post' )
        );
        do_action( 'shortcode_directives_action_unregister_directives_post' );
        return $aData;
    }

}