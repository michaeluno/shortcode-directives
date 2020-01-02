<?php

/**
 * Directive: `$post_column`
 *
 * Usage: [$post_meta {meta key1}={meta value1} {meta key2}={meta value2} {meta key3}={meta value3}...]
 * or [$post_meta {meta key} {meta value}]
 * Example:
 * ```
 * [$post_meta _custom_field=normal]
 * [$post_meta _count_descendants=0 _count_direct_children=3]
 * ```
 * Limitatin: `to` and `action` are reserved. So the meta name of `to` or `action` cannot be used with the above format. Use the below instead.
 * ```
 * [$post_meta _my_meta_key value]
 * [$post_meta to something]
 * [$post_meta _my_meta_key value to=parent aciton=delete]
 * ```
 */
class ShortcodeDirectives_Directive_post_meta extends ShortcodeDirectives_Directive_Base {

    public $aTypes   = array(
        'post',
        'comment'
    );

    public $aDirectives = array(
        '$post-meta',
        '$post_meta', // alias of $post-column
        '$meta',      // alias of $post_column
    );

    public $aSubCommands = array();

    public $aOptions = array(
        // Specifies the target to apply the diected operation
        // multiple values can be set separated by commas
        'to'        => 'self',     // self, parent, children, descendants, {post id}
        'action'    => 'add',      // add / delete
    );

    /**
     * @param   array $aAttributes
     * @param   array $aSubject
     * @param   array $aData
     * @return  string|WP_Error|WP_Error[]|array
     */
    protected function _doAction( array $aAttributes, $aSubject, $aData ) {

        $_aCommands      = $this->_getSubCommands( $aAttributes, $this->aSubCommands );
        $_iThisPostID    = ( integer ) $this->getElement( $aSubject, array( 'ID' ), 0 );

        $_aKeyValues     = $this->getElementsOfAssociativeKeys( $aAttributes );
        $_aOptions       = $_aKeyValues + $this->aOptions;
        unset( $_aKeyValues[ 'to' ] );
        if ( isset( $_aCommands[ 0 ] ) ) {
            $_aKeyValues[ $_aCommands[ 0 ] ] = $this->getElement( $_aCommands, array( 1 ), null );
        }
        if ( empty( $_aKeyValues ) ) {
            return 'No key value pairs specified: ' . $_iThisPostID;
        }

        $_sAction   = $this->getElement( $_aOptions, array( 'action' ), 'add' );
        $_sAction   = strtolower( $_sAction );
        $_isTo      = $this->getElement( $_aOptions, array( 'to' ), 'self' ); // (integer|string) the target entity (post|comment)
        $_aPosts    = $this->_getTargetPosts( $aSubject, $_isTo );
        if ( empty( $_aPosts ) ) {
            return new WP_Error( 'no_posts_found', 'No posts could not be found to perform the operation. To: ' . $_isTo . '. Post ID: ' . $_iThisPostID );
        }

        $_aResults  = array();
        foreach( $_aPosts as $_iPostID => $_aPost ) {
            $_iPostID    = ( integer ) $this->getElement( $_aPost, array( 'ID' ), 0 );
            if ( ! $_iPostID && ! $_iThisPostID ) { // to: self - for a new post the post id is 0, for updating a post, a post id has a value
                $this->_aSavePost = array( $_iPostID => $_aKeyValues );
                continue;
            }
            $_aThisResults = 'delete' === $_sAction
                ? $this->___getPostMetaDeleted( $_iPostID, $_aKeyValues )
                : $this->___getPostMetaUpdated( $_iPostID, $_aKeyValues );
            $_aResults = array_merge( $_aResults, $_aThisResults );
        }
        return $_aResults;

    }

        /**
         * @param $iPostID
         * @param array $aKeyValues
         * @see wpdb::delete()
         */
        private function ___getPostMetaDeleted( $iPostID, array $aKeyValues ) {
            $_aResults = array();
            foreach( $aKeyValues as $_sKey => $_mValue ) {
                $_ibResult = $GLOBALS[ 'wpdb' ]->delete(
                    $GLOBALS[ 'wpdb' ]->postmeta,
                    array( 'post_id' => $iPostID, 'meta_key' => $_sKey ),
                    array( '%d', '%s' )
                );
                $_aResults[] = $_ibResult
                    ? "{$_ibResult} row(s) have been deleted. Meta Key: {$_sKey} Value: {$_mValue}"
                    : "Failed to delete rows. Meta Key: {$_sKey} Value: {$_mValue}";
            }
            return $_aResults;
        }
        /**
         * @param  integer   $iPostID
         * @param  array     $aKeyValues
         * @param  string    $sAction       The action. add | delete
         * @return array|WP_Error[]      Messages that indicate the result of the operation.
         */
        private function ___getPostMetaUpdated( $iPostID, array $aKeyValues ) {
            $_aResults = array();
            foreach( $aKeyValues as $_sKey => $_mValue ) {
                $_ibResult   = update_post_meta( $iPostID, $_sKey, $_mValue );
                $_aResults[] = is_integer( $_ibResult )
                    ? "A new meta field was added. Post ID: {$iPostID} Meta ID: {$_ibResult} Key: {$_sKey} Value: {$_mValue}"
                    : ( $_ibResult
                        ? "A meta field was added. Post ID: {$iPostID} Key: {$_sKey} Value: {$_mValue}"
                        : "Failed to add a meta value. Post ID: {$iPostID} Key: {$_sKey} Value: {$_mValue}"
                    );
            }
            return $_aResults;
        }

    /**
     * @remark  For the comment type, this is not called.
     * @param   integer $iPostID      The post ID
     * @param   array   $aKeyValues   Meta data to update
     * @param   bool    $bUpdate      Whether to update the post or nely insert a post.
     * @return  array
     */
    protected function _savePost( $iPostID, array $aKeyValues, $bUpdate ) {
        $_aResults = array();
        if ( ! $iPostID ) {
            $_aResults[] = 'Trying to update post meta but no post ID is given. ' . $this->getArrayRepresentation( $aKeyValues );
            return $_aResults;
        }
        return $this->___getPostMetaUpdated( $iPostID, $aKeyValues );
    }

}