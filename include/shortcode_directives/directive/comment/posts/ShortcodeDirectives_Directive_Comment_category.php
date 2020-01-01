<?php

/**
 * Directive: $category
 */
class ShortcodeDirectives_Directive_Comment_category extends ShortcodeDirectives_Directive_Comment_taxnomomy {

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
    protected function _doAction( array $aAttributes, $oSubject, $aData ) {
        $_oPost       = get_post( $oSubject );
        $_boTaxonomy  = $this->___getFirstFoundPublicHierarchicalTaxonomy( $_oPost );
        if ( ! $_boTaxonomy ) {
            return;
        }
        return parent::_doAction(
            array( 'slug'  => $_boTaxonomy->name ) + $aAttributes + $this->aOptions,
            $oSubject,
            $aData
        );

    }

        private function ___getFirstFoundPublicHierarchicalTaxonomy( WP_Post $oPost ) {
            $_aTaxonomies = get_object_taxonomies( $oPost, 'objects' );
            foreach( $_aTaxonomies as $_oTaxonomy ) {
                if ( ! $_oTaxonomy->hierarchical ) {
                    continue;
                }
                if ( 'post_format' === $_oTaxonomy->name ) {
                    continue;
                }
                if ( ! $_oTaxonomy->public ) {
                    continue;
                }
                return $_oTaxonomy; // first found
            }
            return false;
        }

}