<?php

/**
 * Directive: `$post_parent`
 *
 * Usage: [$post_parent {target parent post ID}]
 * Example:
 * ```
 * [$post_parent 1451]
 * ```
 * ```
 * [$post_parent 0]
 * ```
 */
class ShortcodeDirectives_Directive_post_parent extends ShortcodeDirectives_Directive_post_column {

    public $aTypes   = array(
        'post',
        'comment'
    );

    public $aDirectives = array(
        '$post_parent',
        '$post_parent', // alias of $post_parent
        '$parent',      // alias of $post_parent
    );

    public $aSubCommands = array();

    /**
     * @param   array $aAttributes
     * @param   array $aSubject
     * @param   array $aData
     * @return  string|WP_Error|WP_Error[]|array
     */
    protected function _doAction( array $aAttributes, $aSubject, $aData ) {

        $_aCommands      = $this->_getSubCommands( $aAttributes, $this->aSubCommands );
        $_iThisPostID    = ( integer ) $this->getElement( $aSubject, array( 'ID' ), 0 );
        $_iParentID      = ( integer ) $this->getElement( $_aCommands, array( 0 ), 0 );
        if ( ! $_iParentID ) {
            return new WP_Error( 'missing_parent_id', 'A post parent ID is missing. Post ID: ' . $_iThisPostID );
        }

        $aAttributes[ 'column' ] = 'post_parent';
        return parent::_doAction( $aAttributes, $aSubject, $aData );


    }

}