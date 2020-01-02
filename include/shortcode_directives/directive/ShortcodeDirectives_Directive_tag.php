<?php

/**
 * Directive:  `$tag`
 * Usage: [$tag option_name=option_value value1 value2 value3...]
 *
 * ### Adding terms
 * ```
 * [$tag Apple Banana "Apple Pie"]
 * ```
 *
 * ### Removing terms
 * ```
 * [$tag remove="tip, sticky"]
 * [$tag action=remove tip sticky]
 * ```
 *
 * ```
 * [$tag slug=post_tag to=self apple banana "Apple Pie"]           - applies to the submitted post itself. default: /self is omitted
 * [$tag slug=post_tag to=parent apple banana "Apple Pie"]         - applies to the parent post
 * [$tag slug=post_tag to=children apple banana "Apple Pie"]       - applies to all the child post
 * [$tag slug=post_tag to=descendants apple banana "Apple Pie"]    - applies to all the child post
 * [$tag slug=post_tag to={post id} apple banana "Apple Pie"]      - applies to the post specified with {post id}. Set a post ID in the part `{post id}`
 * [$tag action=remove slug=post_tag apple banana "Apple Pie"]        - removes passed tags.
 * ```
 */
class ShortcodeDirectives_Directive_tag extends ShortcodeDirectives_Directive_taxnomomy {

    public $aTypes   = array(
        'post',
        'comment'
    );

    public $aDirectives = array(
        '$tag',
    );

    /**
     * @param  array $aAttributes
     * @param  array $aSubject
     * @return mixed|void
     */
    protected function _doAction( array $aAttributes, $aSubject, $aData ) {

        $_iThisPostID = $this->getElement( $aSubject,'ID' );
        $_sPostType   = $this->getElement( $aSubject, array( 'post_type' ), '' );
        $_boTaxonomy  = $this->_getFirstFoundPublicTaxonomy( $_sPostType, false );
        if ( ! $_boTaxonomy ) {
            return 'A non-hierarchical taxonomy was not found for the post. Post ID: ' . $_iThisPostID . ' Post Type: ' . $_sPostType;
        }
        return parent::_doAction(
            array( 'slug'  => $_boTaxonomy->name ) + $aAttributes + $this->aOptions,
            $aSubject,
            $aData
        );

    }

        /**
         * @param integer $iPostID
         *
         * @return bool|WP_Taxonomy
         */
        private function ___getFirstFoundPublicNonHierarchicalTaxonomy( $iPostID ) {
            if ( ! $iPostID ) {
                return false;
            }
            return $this->_getFirstFoundPublicTaxonomy( $iPostID, false );
        }

}