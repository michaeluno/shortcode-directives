<?php

/**
 * Directive: `$post_column`
 *
 * Usage: [$post_column column={column name} sub-command1 sub-command2 sub-command3...]
 * Example:
 * ```
 * [$post_column column=post_parent 2354]
 * [$post_column column=post_status pending to=parent]
 * ```
 */
class ShortcodeDirectives_Directive_post_column extends ShortcodeDirectives_Directive_Base {

    public $aTypes   = array(
        'post',
        'comment'
    );

    public $aDirectives = array(
        '$post-column',
        '$post_column', // alias of $post-column
        '$column',      // alias of $post_column
    );

    public $aSubCommands = array();

    protected function _initialize() {
        unset( $this->___iNewValue );
        parent::_initialize();
    }

    /**
     * @param   array $aAttributes
     * @param   array $aSubject
     * @param   array $aData
     * @return  string|WP_Error|WP_Error[]|array
     */
    protected function _doAction( array $aAttributes, $aSubject, $aData ) {

        $_aCommands      = $this->_getSubCommands( $aAttributes, $this->aSubCommands );
        $_iThisPostID    = ( integer ) $this->getElement( $aSubject, array( 'ID' ), 0 );

        $_aOptions      = $this->getElementsOfAssociativeKeys( $aAttributes ) + $this->aOptions;
        $_sColumnName   = $this->getElement( $_aOptions, array( 'column' ), '' ); // (integer|string) the target entity (post|comment)
        if ( ! $_sColumnName ) {
            return 'The column name is not specified. Post ID: ' . $_iThisPostID;
        }

        $_isTo      = $this->getElement( $_aOptions, array( 'to' ), 'self' ); // (integer|string) the target entity (post|comment)
        $_aPosts    = $this->_getTargetPosts( $aSubject, $_isTo );
        if ( empty( $_aPosts ) ) {
            return new WP_Error( 'no_posts_found', 'No posts could not be found to perform the operation. To: ' . $_isTo . '. Post ID: ' . $_iThisPostID );
        }

        $_snValue   = $this->getElement( $_aCommands, array( 0 ), null );
        if ( null === $_snValue ) {
            return new WP_Error( 'value_not_set', 'A value is not set. Post ID: ' . $_iThisPostID );
        }

        $_aResults  = array();
        foreach( $_aPosts as $_iPostID => $_aPost ) {
            $_iPostID    = ( integer ) $this->getElement( $_aPost, array( 'ID' ), 0 );
            $_aArguments = array(
                'ID'            => $_iPostID,
                $_sColumnName   => $_snValue,
            );
            if ( 'post' === $this->_sCurrentType && $_iThisPostID === $_iPostID ) { // to: self - for a new post the post id is 0, for updating a post, a post id has a value
                $_aArgumentsCopy    = $_aArguments;
                unset( $_aArgumentsCopy[ 'ID' ] );
                $this->_aInsertPost = $_aArgumentsCopy;
                continue;
            }
            $_aResults[] = $this->___getPostUpdated( $_aArguments );
        }
        return $_aResults;

    }
        /**
         * @param array $aArguments
         * @return string|WP_Error      The message that indicates the result.
         */
        private function ___getPostUpdated( array $aArguments ) {
            $_ioResult = wp_update_post( $aArguments );
            if ( is_wp_error( $_ioResult ) ) {
                $_ioResult->add( 'failed_wp_update_post', 'wp_update_post() returned an error.' );
                return $_ioResult;
            }
            $_sArguments = $this->getArrayRepresentation( $aArguments );
            return $_ioResult
                ? "Updated " . $_sArguments
                : new WP_Error( 'failed_to_update', 'Could not update the post: ' . $_sArguments );
        }
    
}