<?php

/**
 * Directive: $category
 */
class ShortcodeDirectives_Directive_Comment_category_old extends ShortcodeDirectives_Directive_Base {

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

    public $aOptions = array(
        'add'    => '',
        'remove' => '',
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
        $aAttributes = $aAttributes + $this->aOptions;

        // Add hierarchical taxonomy terms
        $_aAdd         = $this->getCommaDelimitedElements( $aAttributes[ 'add' ] );
        $_aAddTermIDs  = array();
        foreach( $_aAdd as $_sTerm ) {
            $_aTerm = term_exists( $_sTerm, $_boTaxonomy->name );
            if ( empty( $_aTerm ) ) {
                $_aoResult = wp_insert_term( $_sTerm, $_boTaxonomy->name );
                $_iTermID  = isset( $_aoResult[ 'term_id' ] ) ? $_aoResult[ 'term_id' ] : 0;
                do_action( 'shortcode_directives_action_did_directive', 'Post ID: ' . $_oPost->ID . ' Created non-hierarchical taxonomy term: ' . $_iTermID, $_aoResult );
                if ( ! $_iTermID ) {
                    continue;
                }
                $_aAddTermIDs[] = $_iTermID;
            }
            $_aAddTermIDs[] = $_aTerm[ 'term_id' ];
        }
        if ( ! empty( $_aAddTermIDs ) ) {
            $_aboResult = wp_set_post_terms(
                $_oPost->ID,
                $_aAddTermIDs,      // adding terms - for hierarchical taxonomies, integer values must be passed
                $_boTaxonomy->name, // taxonomy slug
                true         // append
            );
            do_action( 'shortcode_directives_action_did_directive', 'Post ID: ' . $_oPost->ID . ' Added non-hierarchical taxonomy terms: ' . implode( ', ', $_aAdd ), $_aboResult );
        }

        // Remove hierarchical taxonomy terms
        $_aRemove    = $this->getCommaDelimitedElements( $aAttributes[ 'remove' ] );
        if ( ! empty( $_aRemove ) ) {
            $_boResult = wp_remove_object_terms( $_oPost->ID, $_aRemove, $_boTaxonomy->name );
            do_action( 'shortcode_directives_action_did_directive', 'Post ID: ' . $_oPost->ID . ' Removed tags: ' . implode( ', ', $_aRemove ), $_boResult );
        }

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