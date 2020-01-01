<?php
/**
 * Class ShortcodeDirectives_Loader
 */


/**
 * # Cases
 * ## Posts
 *  -> to self                    *       barely      $directive /self            option1 option2 option3...
 *  -> to a parent post (default) *****   frequent    $directive (/parent)        option1 option2 option3...
 *  -> to children                ***     sometimes   $directive /children        option1 option2 option3...
 *  -> to posts with a taxonomy   **      rare        $directive /taxonomy-{slug} option1 option2 option3...
 *  -> to a post with an ID       **      rare        $directive /post-{id}       option1 option2 option3...
 *  -> to comments                ****    often       $directive /comments        option1 option2 option3...
 * ## Comments
 *  -> to self                    *       barely      $directive /self            option1 option2 option3...
 *  -> to post (default)          *****   frequent    $directive (/post)          option1 option2 option3...
 *  -> to a parent comment        ****    often       $directive /parent          option1 option2 option3...
 *  -> to child comments          ***     sometimes   $directive /children        option1 option2 option3...
 *  -> to a comment with an ID    **      rare        $directive /comment-{id}    option1 option2 option3...
 */
class ShortcodeDirectives_Loader extends ShortcodeDirectives_PluginUtility {

    /**
     * Performs necessary set-ups.
     */
    public function     __construct() {

        $_oOption             = ShortcodeDirectives_Option::getInstance();
        $_aPostTypes          = $_oOption->getAsArray( $_oOption->get( array( 'post_types' ) ) );
        $_aPostTypes_Posts    = array();
        $_aPostTypes_Comments = array();
        foreach( $_aPostTypes as $_sPostType => $_aPostType ) {
            $_aAreas = $this->getElementAsArray( $_aPostType, array( 'areas' ) );
            if ( $this->getElement( $_aAreas, array( 'post' ) ) ) {
                $_aPostTypes_Posts[] = $_sPostType;
            }
            if ( $this->getElement( $_aAreas, array( 'comment' ) ) ) {
                $_aPostTypes_Comments[] = $_sPostType;
            }
        }

        new ShortcodeDirectives_Log;
        new ShortcodeDirectives_Area_Posts( $_aPostTypes_Posts );
        new ShortcodeDirectives_Area_Comments( $_aPostTypes_Comments );

        $this->___loadDirectives();

    }

    private function ___loadDirectives() {

        new ShortcodeDirectives_Directive_Comment_taxnomomy;
        new ShortcodeDirectives_Directive_Comment_category;
        new ShortcodeDirectives_Directive_Comment_tag;
        new ShortcodeDirectives_Directive_Comment_status;
        new ShortcodeDirectives_Directive_Comment_parent;
        new ShortcodeDirectives_Directive_Comment_comment;

    }

}