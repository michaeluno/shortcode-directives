<?php
/**
 * Class ShortcodeDirectives_Loader
 */


/**
 * # Cases
 * ## Posts
 *  -> to self                    *       barely      $directive to=self            option1 option2 option3...
 *  -> to a parent post (default) *****   frequent    $directive to=parent        option1 option2 option3...
 *  -> to children                ***     sometimes   $directive to=children        option1 option2 option3...
 *  -> to posts with a taxonomy   **      rare        $directive to=taxonomy-{slug} option1 option2 option3...
 *  -> to a post with an ID       **      rare        $directive to={post id}       option1 option2 option3...
 *  -> to comments                ****    often       $directive to=comments        option1 option2 option3...
 * ## Comments
 *  -> to self                    *       barely      $directive to=self            option1 option2 option3...
 *  -> to post (default)          *****   frequent    $directive to=post          option1 option2 option3...
 *  -> to a parent comment        ****    often       $directive to=parent          option1 option2 option3...
 *  -> to child comments          ***     sometimes   $directive to=children        option1 option2 option3...
 *  -> to a comment with an ID    **      rare        $directive to=comment-{id}    option1 option2 option3...
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

        $this->___loadDirectives( $_aPostTypes_Posts );

        new ShortcodeDirectives_Directive_Debug_PostMeta( $_aPostTypes_Posts );

    }

    private function ___loadDirectives( array $aPostTypeSlugs ) {

        new ShortcodeDirectives_Directive_taxnomomy( $aPostTypeSlugs );
        new ShortcodeDirectives_Directive_category( $aPostTypeSlugs );
        new ShortcodeDirectives_Directive_tag( $aPostTypeSlugs );
        new ShortcodeDirectives_Directive_post_column( $aPostTypeSlugs );
        new ShortcodeDirectives_Directive_post_status( $aPostTypeSlugs );
        new ShortcodeDirectives_Directive_post_parent( $aPostTypeSlugs );
        new ShortcodeDirectives_Directive_post_meta( $aPostTypeSlugs );
        new ShortcodeDirectives_Directive_Comment_comment( $aPostTypeSlugs );

    }

}