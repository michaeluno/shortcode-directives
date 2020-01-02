<?php

/**
 * Loads the Directive Shortcode component.
 *
 */
class ShortcodeDirectives_Area_Posts extends ShortcodeDirectives_Area_Base {

    protected function _construct() {

        // These hooks will be called in order to temporarily disable hooks to prevent recursive calls.
        add_action( 'shortcode_directives_action_register_hooks_of_area_posts', array( $this, 'replyToRegisterHooks' ) );
        add_action( 'shortcode_directives_action_unregister_hooks_of_area_posts', array( $this, 'replyToUnregisterHooks' ) );
        $this->replyToRegisterHooks();

    }

    public function replyToRegisterHooks() {
        add_filter( 'wp_insert_post_data', array( $this, 'replyToPreprocessPost' ), 10, 2 );

    }
    public function replyToUnregisterHooks() {
        remove_filter( 'wp_insert_post_data', array( $this, 'replyToPreprocessPost' ), 10 );
    }


    /**
     * @param array $aData    An array of slashed post data.
     * ### Structure
     *  [post_author] => (integer, length: 1) 1
     *  [post_date] => (string, length: 19) 2020-01-02 12:24:08
     *  [post_date_gmt] => (string, length: 19) 2020-01-02 03:24:08
     *  [post_content] => (string, length: 38) <p>[$post_status to=parent trash]</p>
     *  [post_content_filtered] => (string, length: 0)
     *  [post_title] => (string, length: 0)
     *  [post_excerpt] => (string, length: 0)
     *  [post_status] => (string, length: 7) publish
     *  [post_type] => (string, length: 4) note
     *  [comment_status] => (string, length: 4) open
     *  [ping_status] => (string, length: 6) closed
     *  [post_password] => (string, length: 0)
     *  [post_name] => (string, length: 0)
     *  [to_ping] => (string, length: 0)
     *  [pinged] => (string, length: 0)
     *  [post_modified] => (string, length: 19) 2020-01-02 12:24:08
     *  [post_modified_gmt] => (string, length: 19) 2020-01-02 03:24:08
     *  [post_parent] => (integer, length: 4) 2056
     *  [menu_order] => (integer, length: 1) 0
     *  [post_mime_type] => (string, length: 0)
     *  [guid] => (string, length: 0)
     * @param array $aPost    An array of sanitized, but otherwise unmodified post data.
     * ### Structure
     *  [post_author] => (integer, length: 1) 1
     *  [post_content] => (string, length: 38) <p>[$post_status to=parent trash]</p>
     *  [post_content_filtered] => (string, length: 0)
     *  [post_title] => (string, length: 0)
     *  [post_excerpt] => (string, length: 0)
     *  [post_status] => (string, length: 7) publish
     *  [post_type] => (string, length: 4) note
     *  [comment_status] => (string, length: 0)
     *  [ping_status] => (string, length: 0)
     *  [post_password] => (string, length: 0)
     *  [to_ping] => (string, length: 0)
     *  [pinged] => (string, length: 0)
     *  [post_parent] => (integer, length: 4) 2056
     *  [menu_order] => (integer, length: 1) 0
     *  [guid] => (string, length: 0)
     *  [import_id] => (integer, length: 1) 0
     *  [context] => (string, length: 0)
     *  [tax_input] => Array(
     *      [note_tag] => Array()
     *  )
     *  [ID] => (integer, length: 1) 0
     *  [filter] => (string, length: 2) db
     * @return mixed
     * @callback filter wp_insert_post_data
     */
    public function replyToPreprocessPost( $aData, $aPost ) {

        if ( ! $this->_isAuthorizedUser( ( integer ) $this->getElement( $aData, 'post_author' ), $aPost ) ) {
            return $aData;
        }

        do_action( 'shortcode_directives_action_register_directives_post', $aPost, $aData );
        $aData[ 'post_content' ] = $this->_processShortcode(
            wp_unslash( $aData[ 'post_content' ] ), // @todo remove wp_unslash() if possible; test it.
            $this->_getDirectiveNames( 'post' )
        );
        $aData = apply_filters( 'shortcode_directives_filter_insert_post_data', $aData );
        do_action( 'shortcode_directives_action_unregister_directives_post' );
        return $aData;

    }

}