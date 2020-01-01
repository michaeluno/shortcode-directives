<?php

/**
 * Directive: `$status`
 *
 * Usage: [$status {post status}]
 * Accepted Sub-command: publish, draft, pending, private, trash
 *
 * Example:
 * ```
 * [$status pending]
 * ```
 * ```
 * [$status draft]
 * ```
 */
class ShortcodeDirectives_Directive_Comment_status extends ShortcodeDirectives_Directive_Comment_Base {

    public $aDirectives = array(
        '$status',
        '$post_status', // alias of $stats
        '$post-status', // alias of $stats
    );

    /**
     * @param array $aAttributes
     */
    protected function _doAction( array $aAttributes, $oSubject, $aCommentData ) {

        $_aCommands      = $this->_getSubCommands( $aAttributes );
        if ( ! isset( $_aCommands[ 0 ] ) ) {
            return;
        }
        $_sPostStatus   = $_aCommands[ 0 ];
        $_iPostID       = $aCommentData[ 'comment_post_ID' ];

        $_aPostStatuses = array_keys( get_post_statuses() );
        if ( ! in_array( $_sPostStatus, $_aPostStatuses ) ) {
            return;
        }

        return wp_update_post(
            array(
                'ID'            => $_iPostID,
                'post_status'   => $_aCommands[ 0 ],
            )
        );

    }

}