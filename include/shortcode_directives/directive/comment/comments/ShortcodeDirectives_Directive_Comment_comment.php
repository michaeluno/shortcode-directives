<?php

/**
 * Applies some actions on the parent comment.
 *
 * Use this for example to remove a comment by replying to the subject comment.
 * If commenting on the post, no action will be taken.
 *
 * Directive: `$comment`
 * Sub-commands: delete, remove, hold, disapprove, trash, spam
 * Usage: [$comment {sub-command}]
 * Examples:
 * ```
 * [$comment delete]
 * ```
 * ```
 * [$comment hold]
 * ```
 */
class ShortcodeDirectives_Directive_Comment_comment extends ShortcodeDirectives_Directive_Comment_Base {

    public $aDirectives = array(
        '$comment',
//        '$comment_status',
//        '$comment-status',
    );

    public $aOptions = array(
//        'delete'     => '',
//        'remove'     => '',
//        'hold'       => '',
//        'disapprove' => '',
//        'trash'      => '',
//        'spam'       => '',
    );

    /**
     * @param  array $aAttributes
     */
    protected function _doAction( array $aAttributes, $oSubject, $aCommentData ) {

        $aAttributes  = $aAttributes + $this->aOptions;
        $aCommentData = $aCommentData + array( 'comment_parent' => 0 );
        if ( ! $aCommentData[ 'comment_parent' ] ) {
            return;
        }
        $_iParentComment = $aCommentData[ 'comment_parent' ];
        $_aCommands      = $this->_getSubCommands( $aAttributes );

        if ( ! empty( array_intersect( array( 'remove', 'delete' ), $_aCommands ) ) ) {
            $_bResult = wp_delete_comment( $_iParentComment, true );
            do_action( 'shortcode_directives_action_did_directive', 'Action: Remove, Comment ID: ' . $_iParentComment, $_bResult, $_aCommands );
            return;
        }
        if ( ! empty( array_intersect( array( 'hold', 'disapprove' ), $_aCommands ) ) ) {
            $_boResult = wp_set_comment_status( $_iParentComment, 'hold', false );
            do_action( 'shortcode_directives_action_did_directive', 'Action: Hold, Comment ID: ' . $_iParentComment, $_boResult, $_aCommands );
            return;
        }
        if ( in_array( 'trash', $_aCommands, true ) ) {
            $_bResult = wp_trash_comment( $_iParentComment );
            do_action( 'shortcode_directives_action_did_directive', 'Action: Trash, Comment ID: ' . $_iParentComment, $_bResult, $_aCommands );
            return;
        }
        if ( in_array( 'spam', $_aCommands, true ) ) {
            $_boResult = wp_set_comment_status( $_iParentComment, 'spam', false );
            do_action( 'shortcode_directives_action_did_directive', 'Action: Spam, Comment ID: ' . $_iParentComment, $_boResult, $_aCommands );
            return;
        }

    }

}