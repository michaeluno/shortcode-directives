<?php

/**
 * Directive: $category
 */
class ShortcodeDirectives_Directive_category extends ShortcodeDirectives_Directive_taxnomomy {

    /**
     * Supported types that represent the area to check the directive string.
     * @var array
     */
    public $aTypes   = array(
        'post',
        'comment'
    );

    public $aDirectives = array(
        '$category',
    );

    /**
     * @param array $aAttributes
     */
    protected function _doAction( array $aAttributes, $aSubject, $aData ) {

        $_iThisPostID = $this->getElement( $aSubject,'ID' );
        $_sPostType   = $this->getElement( $aSubject, array( 'post_type' ), '' );
        $_boTaxonomy  = $this->_getFirstFoundPublicTaxonomy( $_sPostType, true );
        if ( ! $_boTaxonomy ) {
            return 'A hierarchical taxonomy was not found for the post. Post ID: ' . $_iThisPostID . ' Post Type: ' . $_sPostType;
        }
        return parent::_doAction(
            array( 'slug'  => $_boTaxonomy->name ) + $aAttributes + $this->aOptions,
            $aSubject,
            $aData
        );

    }

}