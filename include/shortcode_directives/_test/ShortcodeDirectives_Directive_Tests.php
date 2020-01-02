<?php


class ShortcodeDirectives_Directive_Tests {

    public $aPostTypeSlugs;

    /**
     * Performs necessary set-ups.
     */
    public function __construct( array $aPostTypeSlugs ) {

        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }

        add_filter( 'the_content', array( $this, 'replyToModifyContent' ) );

        $this->aPostTypeSlugs = $aPostTypeSlugs;

    }
    public function replyToModifyContent( $sPostContent ) {

        if ( ! is_singular() ) {
            return $sPostContent;
        }
        if ( ! is_main_query() ) {
            return $sPostContent;
        }
        $_oPost = get_post();
        if ( ! in_array( $_oPost->post_type, $this->aPostTypeSlugs, true )  ) {
            return $sPostContent;
        }

        $_aPostData = array();
        foreach( ( array ) get_post_custom_keys( $_oPost->ID ) as $_sKey ) {    // This way, array will be unserialized; easier to view.
            $_aPostData[ $_sKey ] = get_post_meta( $_oPost->ID, $_sKey, true );
        }
        return empty( $_aPostData )
            ? $sPostContent
            : $sPostContent
                . "<h3>" . ShortcodeDirectives_Registry::NAME . " DEBUG - Post Meta</h3>"
                . "<p><em>(This is only shown when the site debug mode is turned on.)</em></p>"
                . ShortcodeDirectives_Debug::get( $_aPostData );

    }

}