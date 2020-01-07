<?php

/**
 * Directive: `$post_status`
 *
 * Usage: [$post_status {post status}]
 * Accepted Sub-command: publish, draft, pending, private, trash
 *
 * Example:
 * ```
 * [$post_status pending]
 * ```
 * ```
 * [$post_status draft]
 * ```
 */
class ShortcodeDirectives_Directive_post_status extends ShortcodeDirectives_Directive_post_column {

    public $aTypes   = array(
        'post',
        'comment'
    );

    public $aDirectives = array(
        '$post_status',
        '$post-status', // alias of $post_stats
        '$status',      // alias of $post_stats
    );

    public $aSubCommands = array(
        'publish', 'draft', 'pending', 'private', 'trash',
    );

    /**
     * @param   array $aAttributes
     * @param   array $aSubject
     * @param   array $aData
     * @return  string|WP_Error|WP_Error[]|array
     */
    protected function _doAction( array $aAttributes, $aSubject, $aData ) {

        $_aCommands      = $this->_getSubCommands( $aAttributes, $this->aSubCommands );
        $_sPostStatus    = $this->getElement( $_aCommands, array( 0 ), '' );
        $_iThisPostID    = ( integer ) $this->getElement( $aSubject, array( 'ID' ), 0 );
        if ( ! $_sPostStatus ) {
            return new WP_Error( 'subcommand_not_set', 'A sub-command was not set. Post ID: ' . $_iThisPostID );
        }
        $_aPostStatuses   = array_keys( get_post_statuses() );
        $_aPostStatuses[] = 'trash';
        if ( ! in_array( $_sPostStatus, $_aPostStatuses, true ) ) {
            return new WP_Error(
                'post_status_not_exist', 'The post staus does not exist: ' . $_sPostStatus . ' Post ID: ' . $_iThisPostID . '. '
                . 'Supported post statuses: ' . implode( ',', $_aPostStatuses ) . '.'
            );
        }
        $aAttributes[ '--column' ] = 'post_status';
        return parent::_doAction( $aAttributes, $aSubject, $aData );

    }

}