<?php

/**
 * Directive:  `$tag`
 * Sub-commands: add, remove
 * Usage: [$tag {sub-command}]
 * Examples:
 * ```
 * [$tag add="Happy, done, info"]
 * ```
 * ```
 * [$tag remove="tip, sticky"]
 * ```
 *
 * ```
 * [$tag /self apple, banana, "Apple Pie"] - applies to the submitted post itself. default: /self is omitted
 * [$tag /parent apple, banana ] - applies to the parent post
 * [$tag /children apple, banana ] - applies to all the child post
 * [$tag /to-{post id} apple, banana ] - applies to the post specified with {post id}. Set a post ID in the part `{post id}`
 * [$tag /by-name apple, banana ]   - default: /by-name is omitted
 * [$tag /by-slug apple, banana ]   - when `by-name` is used, this is not effective
 * [$tag /remove apple, banana ]   - removes passed tags.
 * [$tag /taxonomy-{taxonomy slug} apple, banana ]   - operates on the specified tag. By default it will look for a first found non-hierarchical taxonomy.
 * ```
 * ```
 * [$tag-remove /self apple,banana]
 * [$tag-remove /parent apple,banana]
 * [$tag-remove /children apple,banana]
 * ```
 */
class ShortcodeDirectives_Directive_Comment_tag_old extends ShortcodeDirectives_Directive_Comment_category {

    /**
     * Supported types that represent the area to check the directive string.
     * @var array
     */
    public $aTypes   = array(
        'post',
        'comment'
    );

    public $aDirectives = array(
        '$tag',
    );

    /**
     * @param  array $aAttributes
     */
    protected function _doAction( array $aAttributes, $oSubject, $aCommentData ) {

        $_oPost       = get_post( $aCommentData[ 'comment_post_ID' ] );
        $_boTaxonomy  = $this->___getFirstFoundPublicNonHierarchicalTaxonomy( $_oPost );
        if ( ! $_boTaxonomy ) {
            return;
        }
        $aAttributes = $aAttributes + $this->aOptions;

        // Add non-hierarchical taxonomy terms
        $_aAdd         = $this->getCommaDelimitedElements( $aAttributes[ 'add' ] );
        if ( ! empty( $_aAdd ) ) {
            $_aboResult = wp_set_post_terms(
                $_oPost->ID,
                $_aAdd,             // adding terms
                $_boTaxonomy->name, // taxonomy slug
                true         // append
            );
            do_action( 'shortcode_directives_action_did_directive', 'Post ID: ' . $_oPost->ID . ' Added tags: ' . implode( ', ', $_aAdd ), $_aboResult );
        }

        // Remove non-hierarchical taxonomy terms
        $_aRemove    = $this->getCommaDelimitedElements( $aAttributes[ 'remove' ] );
        if ( ! empty( $_aRemove ) ) {
            $_boResult = wp_remove_object_terms( $_oPost->ID, $_aRemove, $_boTaxonomy->name );
            do_action( 'shortcode_directives_action_did_directive', 'Post ID: ' . $_oPost->ID . ' Removed tags: ' . implode( ', ', $_aRemove ), $_boResult );
        }

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