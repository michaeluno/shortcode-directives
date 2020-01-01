<?php

/**
 * Directive:  `$taxonomy`
 * Usage: [$taxonomy option_name=option_value value1 value2 value3...]
 *
 * ### Adding terms
 * ```
 * [$taxonomy slug=post_tag Apple Banana "Apple Pie"]
 * ```
 *
 * ### Removing terms
 * ```
 * [$taxonomy remove="tip, sticky"]
 * [$taxonomy action=remove tip sticky]
 * ```
 *
 * ```
 * [$taxonomy slug=post_tag to=self apple banana "Apple Pie"]           - applies to the submitted post itself. default: /self is omitted
 * [$taxonomy slug=post_tag to=parent apple banana "Apple Pie"]         - applies to the parent post
 * [$taxonomy slug=post_tag to=children apple banana "Apple Pie"]       - applies to all the child post
 * [$taxonomy slug=post_tag to=descendants apple banana "Apple Pie"]    - applies to all the child post
 * [$taxonomy slug=post_tag to={post id} apple banana "Apple Pie"]      - applies to the post specified with {post id}. Set a post ID in the part `{post id}`
 * [$taxonomy action=remove slug=post_tag apple banana "Apple Pie"]     - removes passed terms.
 * [$taxonomy action=remove-all slug=post_tag]                          - removes all the associated terms with the post.
 * [$taxonomy action=delete slug=post_tag apple banana "Apple Pie"]     - deletes passed terms from the database if the term is associated with the post.
 * [$taxonomy action=delete-all slug=post_tag apple banana "Apple Pie"] - deletes all the associated terms with the post from the database.
 * ```
 */
class ShortcodeDirectives_Directive_Comment_taxnomomy extends ShortcodeDirectives_Directive_Comment_Base {


    public $aDirectives = array(
        '$taxonomy',
    );

    public $aOptions = array(
        'add'    => '',
        'remove' => '',
        'delete' => '',     // alias of `remove`
        'to'     => '',     // self, parent, children, descendants, {post id}
        'action' => 'add',  // add / remove / remove-all
    );

    /**
     * @param array $aAttributes
     * @param array|object $oSubject
     * @param mixed $mData
     *
     * @return mixed|void
     */
    protected function _doAction( array $aAttributes, $oSubject, $mData ) {

        $_sTaxonomySlug = $this->getElement( $aAttributes, array( 'slug' ), '' );
        if ( empty( $_sTaxonomySlug ) ) {
            return;
        }

        // Parse attributes
        $_aTerms    = $this->getElementsOfNumericKeys( $aAttributes );
        $_aOptions  = $this->getElementsOfAssociativeKeys( $aAttributes ) + $this->aOptions;

        $_isTo      = $this->getElement( $_aOptions, array( 'to' ), '' ); // (integer|string) the target entity (post|comment)
        $_sAction   = $this->getElement( $_aOptions, array( 'action' ), '' ); // (integer|string) the target entity (post|comment)
        $_sAction   = strtolower( $_sAction );

        $_aPosts    = $this->___getTargetPosts( $oSubject, $_isTo );

        // Case: remove="foo, bar"
        $_aRemoves  = $this->getCommaDelimitedElements( $this->getElement( $_aOptions, array( 'remove' ), '' ) );
        $_aRemoved  = $this->___removeTerms( $_aPosts, $_sTaxonomySlug, $_aRemoves );

        // Case: add="foo, bar"
        $_aAdds     = $this->getCommaDelimitedElements( $this->getElement( $_aOptions, array( 'add' ), '' ) );
        $_aAdded    = $this->___addTerms( $_aPosts, $_sTaxonomySlug, $_aAdds );

        if ( in_array( $_sAction, array( 'delete', ), true ) ) {
            return $this->___deleteTerms( $_aPosts, $_sTaxonomySlug, $_aTerms );
        }
        if ( in_array( $_sAction, array( 'delete-all', 'delete_all', ), true ) ) {
            return $this->___deleteAllTerms( $_aPosts, $_sTaxonomySlug );
        }
        if ( in_array( $_sAction, array( 'remove-all', 'remove_all', ), true ) ) {
            return array_merge( $_aRemoved, $this->___removeAllTerms( $_aPosts, $_sTaxonomySlug ) );
        }
        if ( in_array( $_sAction, array( 'remove', ), true ) ) {
            return array_merge( $_aRemoved, $this->___removeTerms( $_aPosts, $_sTaxonomySlug, $_aTerms ) );
        }
        return array_merge( $_aAdded, $this->___addTerms( $_aPosts, $_sTaxonomySlug, $_aTerms ) );

    }
        /**
         * @param WP_Post $oPost
         * @param integer|string $isTo
         *
         * @return array
         */
        private function ___getTargetPosts( WP_Post $oPost, $isTo ) {

            // Case: to={post id} e.g. to=3455
            if ( is_numeric( $isTo ) ) {
                return array( get_post( $isTo ) );
            }
            // Case: to=self
            if ( 'self' === $isTo ) {
                return array( $oPost );
            }
            // Case: to=parent
            if ( 'parent' === $isTo ) {
                $_oParent = get_post( $oPost->post_parent );
                return empty( $_oParent )
                    ? array()
                    : array( $_oParent );
            }
            // Case: to=children
            if ( 'children' === $isTo ) {
                $_oQuery = new WP_Query();
                $_aPosts = $_oQuery->query(
                    array(
                        'posts_per_page' => -1,
                        'post_parent'    => ( integer ) $oPost->ID,
                        'post_type'      => $oPost->post_type,
                    )
                );
                return $_aPosts;
            }
            // Case: to=descendants
            if ( 'descendants' === $isTo ) {
                return $this->getDescendants( $oPost->ID );
            }

            // Default:
            return array( $oPost );

        }

        /**
         * Deletes the taxonomy terms which are assigned to the target posts from the site database.
         * @param array $aPosts
         * @param $sTaxonomySlug
         */
        private function ___deleteAllTerms( array $aPosts, $sTaxonomySlug ) {
            $_aResults = array();
            foreach( $aPosts as $_oPost ) {
                $_aoSetTerms    = get_the_terms( $_oPost, $sTaxonomySlug );
                if ( is_wp_error( $_aoSetTerms ) ) {
                    // @todo may be do action for logging
                    continue;
                }
                foreach( $_aoSetTerms as $_oTerm ) {
                    $_aResults[] = wp_delete_term( $_oTerm->term_id, $sTaxonomySlug );
                }
            }
            return $_aResults;
        }

        /**
         * Deletes the given taxonomy terms assigned to the target posts from the site database.
         * If the given term is not assigned to the post, no action will be taken.
         * @param array $aPosts
         * @param $sTaxonomySlug
         * @param array $aTerms
         */
        private function ___deleteTerms( array $aPosts, $sTaxonomySlug, array $aTerms ) {
            $_aResults = array();
            if ( empty( $aTerms ) ) {
                return $_aResults;
            }
            foreach( $aPosts as $_oPost ) {
                $_aoSetTerms = get_the_terms( $_oPost, $sTaxonomySlug );
                if ( is_wp_error( $_aoSetTerms ) ) {
                    continue;
                }
                $_aTerms     = array_map( 'strtolower', $aTerms );
                foreach( $_aoSetTerms as $_oTerm ) {
                    if ( ! in_array( strtolower( $_oTerm->name ), $_aTerms, true ) ) {
                        continue;
                    }
                    $_aResults[] = wp_delete_term( $_oTerm->term_id, $sTaxonomySlug );
                }
            }
            return $_aResults;
        }

        private function ___removeAllTerms( array $aPosts, $sTaxonomySlug ) {
            $_aResults = array();
            foreach( $aPosts as $_oPost ) {
                $_aoTerms    = get_the_terms( $_oPost, $sTaxonomySlug );
                if ( is_wp_error( $_aoTerms ) ) {
                    // @todo may be do action for logging
                    continue;
                }
                $_aTerms     = wp_list_pluck( $_aoTerms, 'slug' );
                $_aResults[] = wp_remove_object_terms(
                    $_oPost->ID,
                    $_aTerms,
                    $sTaxonomySlug
                );
            }
            return $_aResults;
        }
        /**
         * @param WP_Post[] $aPosts
         * @param string $sTaxonomySlug
         * @param array $aTerms
         * @param string $sBy
         */
        private function ___removeTerms( array $aPosts, $sTaxonomySlug, array $aTerms ) {
            $_aResults = array();
            if ( empty( $aTerms  ) ) {
                return $_aResults;
            }
            foreach( $aPosts as $_oPost ) {
                $_aResults[] = wp_remove_object_terms(
                    $_oPost->ID,
                    $aTerms,
                    $sTaxonomySlug
                );
            }
            return $_aResults;
        }
        /**
         * @param WP_Post[] $aPosts
         * @param string $sTaxonomySlug
         * @param array $aTerms
         * @param string $sBy
         */
        private function ___addTerms( array $aPosts, $sTaxonomySlug, array $aTerms ) {
            $_aResults = array();
            if ( empty( $aTerms ) ) {
                return $_aResults;
            }
            foreach( $aPosts as $_oPost ) {
                $_aResults[] = wp_set_post_terms(
                    $_oPost->ID,
                    $aTerms,             // adding terms
                    $sTaxonomySlug, // taxonomy slug
                    true         // append
                );
            }
            return $_aResults;
        }

}