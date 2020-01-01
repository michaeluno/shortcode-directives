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
class ShortcodeDirectives_Directive_Comment_tag extends ShortcodeDirectives_Directive_Comment_taxnomomy {

    public $aDirectives = array(
        '$tag',
    );

    /**
     * @param  array $aAttributes
     * @param  WP_Post $oSubject
     * @return mixed|void
     */
    protected function _doAction( array $aAttributes, $oSubject, $aData ) {

        $_boTaxonomy  = $this->___getFirstFoundPublicNonHierarchicalTaxonomy( $oSubject );
        if ( ! $_boTaxonomy ) {
            return;
        }
        return parent::_doAction(
            array( 'slug'  => $_boTaxonomy->name ) + $aAttributes + $this->aOptions,
            $oSubject,
            $aData
        );

    }

        /**
         * @param WP_Post $oPost
         *
         * @return bool|WP_Taxonomy
         */
        private function ___getFirstFoundPublicNonHierarchicalTaxonomy( WP_Post $oPost ) {
            $_aTaxonomies = get_object_taxonomies( $oPost, 'objects' );
            foreach( $_aTaxonomies as $_oTaxonomy ) {
                if ( $_oTaxonomy->hierarchical ) {
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