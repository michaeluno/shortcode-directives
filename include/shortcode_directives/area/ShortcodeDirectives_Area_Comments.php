<?php

/**
 * Loads the Directive Comments component.
 *
 * @requires    WordPress 4.5.0     The third parameter of the `comment_post` action hook.
 */
class ShortcodeDirectives_Area_Comments extends ShortcodeDirectives_Area_Base {

    protected function _construct() {
        add_filter( 'preprocess_comment', array( $this, 'replyToPreprocessComment' ) );
    }

    /**
     * Called before before a posted comment is sanitized and inserted into the database.
     * @param array $aCommentData Comment data.
     */
    public function replyToPreprocessComment( $aCommentData ) {

        if ( ! isset( $aCommentData[ 'comment_post_ID' ] ) ) {
            return $aCommentData;
        }
        // Retrieving the post data as an array (not a WP_Post object) for compatibility with cases of being called from a `wp_insert_post_data` filter callback.
        // With that hook, the post is not created yet thus not possible to have a WP_Post object.
        $_aPost = get_post( $aCommentData[ 'comment_post_ID' ], 'ARRAY_A' );
        if ( ! in_array( $_aPost[ 'post_type' ], $this->aPostTypeSlugs, true ) ) {
            return $aCommentData;
        }
        $_iCommenterID = $this->getElement(
            $aCommentData,
            array( 'user_ID' ),
            $this->getElement( $aCommentData, array( 'user_id' ), 0 )
        );
        if ( ! $this->_isAuthorizedUser( $_iCommenterID, $_aPost ) ) {
            return $aCommentData;
        }

        do_action( 'shortcode_directives_action_register_directives_comment', $_aPost, $aCommentData );
        add_filter( 'pre_comment_content', array( $this, 'replyToFilterCommentPreContent' ), PHP_INT_MAX );
        return $aCommentData;

    }

        public function replyToFilterCommentPreContent( $sContent ) {

            $_sContent = $this->_processShortcode( wp_unslash( $sContent ), $this->_getDirectiveNames( 'comment' ) );
            // Only the shortcode is posted, then do not add a comment.
            if ( '' === trim( $_sContent ) ) {
                add_filter( 'duplicate_comment_id', '__return_false', PHP_INT_MAX );
                add_filter( 'pre_comment_approved', '__return_false', PHP_INT_MAX );
            }
            remove_filter( 'pre_comment_content', array( $this, 'replyToFilterCommentPreContent' ), PHP_INT_MAX );

            do_action( 'shortcode_directives_action_unregister_directives_comment' );
            return $_sContent;

        }




}