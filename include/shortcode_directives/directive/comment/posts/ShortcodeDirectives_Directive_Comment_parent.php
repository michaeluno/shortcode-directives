<?php

/**
 * Directive: `$parent`
 *
 * Usage: [$parent {target parent post ID}]
 * Example:
 * ```
 * [$parent 1451]
 * ```
 * ```
 * [$parent 0]
 * ```
 */
class ShortcodeDirectives_Directive_Comment_parent extends ShortcodeDirectives_Directive_Comment_Base {


    public $aDirectives = array(
        '$parent',
        '$post_parent', // alias of $parent
        '$post-parent', // alias of $parent
    );

    /**
     * @param array $aAttributes
     */
    protected function _doAction( array $aAttributes, $oSubject, $aCommentData ) {
        $_aCommands      = $this->_getSubCommands( $aAttributes );
        if ( ! isset( $_aCommands[ 0 ] ) ) {
            return;
        }
        $_iPostID  = $aCommentData[ 'comment_post_ID' ];
        return wp_update_post(
            array(
                'ID'            => $_iPostID,
                'post_parent'   => $_aCommands[ 0 ],
            )
        );
    }

}