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
class ShortcodeDirectives_Directive_taxnomomy extends ShortcodeDirectives_Directive_Base {

    public $aTypes   = array(
        'post',
        'comment'
    );

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
     * @param array $aSubject
     * @param mixed $mData
     *
     * @return mixed|void
     */
    protected function _doAction( array $aAttributes, $aSubject, $mData ) {

        $_sTaxonomySlug = $this->getElement( $aAttributes, array( 'slug' ), '' );
        if ( empty( $_sTaxonomySlug ) ) {
            return new WP_Error( 'no_slug_specified', 'The slug must be set. e.g. $taxonomy slug=post_tag TermA TermB' );
        }

        // Parse attributes
        $_aTerms      = $this->getElementsOfNumericKeys( $aAttributes );
        $_aOptions    = $this->getElementsOfAssociativeKeys( $aAttributes ) + $this->aOptions;

        $_iThisPostID = ( integer ) $this->getElement( $aSubject, array( 'ID' ), 0 );
        $_isTo        = $this->getElement( $_aOptions, array( 'to' ), 'self' ); // (integer|string) the target entity (post|comment)
        $_sAction     = $this->getElement( $_aOptions, array( 'action' ), '' ); // (integer|string) the target entity (post|comment)
        $_sAction     = strtolower( $_sAction );

        $_aPosts      = $this->_getTargetPosts( $aSubject, $_isTo );

        // Case: remove="foo, bar"
        $_aRemoves  = $this->getCommaDelimitedElements( $this->getElement( $_aOptions, array( 'remove' ), '' ) );
        $_aRemoved  = $this->___removeTerms( $_aPosts, $_sTaxonomySlug, $_aRemoves, $_iThisPostID );

        // Case: add="foo, bar"
        $_aAdds     = $this->getCommaDelimitedElements( $this->getElement( $_aOptions, array( 'add' ), '' ) );
        $_aAdded    = $this->___addTerms( $_aPosts, $_sTaxonomySlug, $_aAdds, $_iThisPostID );

        if ( in_array( $_sAction, array( 'delete', ), true ) ) {
            return $this->___deleteTerms( $_aPosts, $_sTaxonomySlug, $_aTerms, $_iThisPostID );
        }
        if ( in_array( $_sAction, array( 'delete-all', 'delete_all', ), true ) ) {
            return $this->___deleteAllTerms( $_aPosts, $_sTaxonomySlug, $_iThisPostID );
        }
        if ( in_array( $_sAction, array( 'remove-all', 'remove_all', ), true ) ) {
            return array_merge( $_aRemoved, $this->___removeAllTerms( $_aPosts, $_sTaxonomySlug, $_iThisPostID ) );
        }
        if ( in_array( $_sAction, array( 'remove', ), true ) ) {
            return array_merge( $_aRemoved, $this->___removeTerms( $_aPosts, $_sTaxonomySlug, $_aTerms, $_iThisPostID ) );
        }
        return array_merge( $_aAdded, $this->___addTerms( $_aPosts, $_sTaxonomySlug, $_aTerms, $_iThisPostID ) );

    }

        /**
         * Deletes the taxonomy terms which are assigned to the target posts from the site database.
         * @param array $aPosts
         * @param $sTaxonomySlug
         * @param integer $iThisPostID  The post ID of the subject post. For a new post, 0; for an updating post, a value is assigned.
         * @return  array
         */
        private function ___deleteAllTerms( array $aPosts, $sTaxonomySlug, $iThisPostID ) {

            $_aResults = array();
            foreach( $aPosts as $_aPost ) {

                $_iPostID = ( integer ) $this->getElement( $_aPost, array( 'ID' ), 0 );
                // Comment: with an ID, New Post: no ID, Update Post: with an ID
                if ( ! $_iPostID && $iThisPostID === $_iPostID ) {
                    $this->_aSavePost = array(
                        $_iPostID => array(
                            'delete_all_terms' => array( $sTaxonomySlug => $sTaxonomySlug ),
                        )
                    );
                    $_aResults[] = 'It will be handled in the `save_post_{post type slug}` hook.';
                    continue;
                }
                $_aoSetTerms    = get_the_terms( $_iPostID, $sTaxonomySlug );
                if ( is_wp_error( $_aoSetTerms ) ) {
                    $_aResults[] = $_aoSetTerms;
                    continue;
                }
                $_aDeleted       = array();
                foreach( $_aoSetTerms as $_oTerm ) {
                    $_boResult   = wp_delete_term( $_oTerm->term_id, $sTaxonomySlug );
                    if ( true === $_boResult ) {
                        $_aDeleted[] = $_oTerm->term_id;
                        continue;
                    }
                    $_aResults[] = false === $_boResult
                        ? 'Failed to delete term ID: ' . $_oTerm->term_id
                        : $_boResult;
                }
                if ( ! empty( $_aDeleted ) ) {
                    $_aResults[] = 'Deleted term IDs: ' . implode( ', ', $_aDeleted );
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
         * @param integer $iThisPostID  The post ID of the subject post. For a new post, 0; for an updating post, a value is assigned.
         * @return  array   an array holding post data arrays.
         */
        private function ___deleteTerms( array $aPosts, $sTaxonomySlug, array $aTerms, $iThisPostID ) {

            $_aResults = array();
            if ( empty( $aTerms ) ) {
                return $_aResults;
            }
            foreach( $aPosts as $_aPost ) {

                $_iPostID = ( integer ) $this->getElement( $_aPost, array( 'ID' ), 0 );
                // Comment: with an ID, New Post: no ID, Update Post: with an ID
                if ( ! $_iPostID && $iThisPostID === $_iPostID ) {
                    $this->_aSavePost = array(
                        $_iPostID => array(
                            'delete_terms' => array( $sTaxonomySlug => $aTerms ),
                        )
                    );
                    $_aResults[] = 'It will be handled in the `save_post_{post type slug}` hook.';
                    continue;
                }

                $_aoSetTerms = get_the_terms( $_iPostID, $sTaxonomySlug );
                if ( is_wp_error( $_aoSetTerms ) ) {
                    $_aResults[] = $_aoSetTerms;
                    continue;
                }
                $_aTerms     = array_map( 'strtolower', $aTerms );
                foreach( $_aoSetTerms as $_oTerm ) {
                    if ( ! in_array( strtolower( $_oTerm->name ), $_aTerms, true ) ) {
                        continue;
                    }
                    $_boResult   = wp_delete_term( $_oTerm->term_id, $sTaxonomySlug );
                    if ( true === $_boResult ) {
                        $_aResults[] = 'Deleted term ID: ' . $_oTerm->term_id;
                        continue;
                    }
                    if ( false === $_boResult ) {
                        $_aResults[] = 'Could not delete the non-existent term: ' . $_oTerm->term_id;
                    }
                    if ( 0 === $_boResult ) {
                        $_aResults[] = 'Could not delete the default term (category): ' . $_oTerm->term_id;
                    }
                    if ( is_wp_error( $_boResult ) ) {
                        $_aResults[] = 'The taxonomy does not exist: ' . $_oTerm->term_id;
                    }
                }

            }
            return $_aResults;
        }

        /**
         * @param array $aPosts
         * @param $sTaxonomySlug
         * @param integer $iThisPostID  The post ID of the subject post. For a new post, 0; for an updating post, a value is assigned.
         *
         * @return array
         */
        private function ___removeAllTerms( array $aPosts, $sTaxonomySlug, $iThisPostID ) {

            $_aResults = array();
            foreach( $aPosts as $_aPost ) {

                $_iPostID = ( integer ) $this->getElement( $_aPost, array( 'ID' ), 0 );
                // Comment: with an ID, New Post: no ID, Update Post: with an ID
                if ( ! $_iPostID && $iThisPostID === $_iPostID ) {
                    $this->_aSavePost = array(
                        $_iPostID => array(
                            'remove_all_terms' => array( $sTaxonomySlug => $sTaxonomySlug ),
                        )
                    );
                    $_aResults[] = 'It will be handled in the `save_post_{post type slug}` hook.';
                    continue;
                }
                $_aoTerms    = get_the_terms( $_iPostID, $sTaxonomySlug );
                if ( is_wp_error( $_aoTerms ) ) {
                    $_aResults[] = $_aoTerms;
                    continue;
                }
                $_aTerms     = wp_list_pluck( $_aoTerms, 'slug' );
                $_boResult   = wp_remove_object_terms(
                    $_iPostID,
                    $_aTerms,
                    $sTaxonomySlug
                );
                $_aResults[] = true === $_boResult
                    ? 'Removed term slugs: ' . implode( ', ', $_aTerms ) . ' from Post ID: ' . $_iPostID
                    : $_boResult;

            }
            return $_aResults;

        }
        /**
         * @param WP_Post[] $aPosts
         * @param string $sTaxonomySlug
         * @param array $aTerms
         * @param string $sBy
         * @param integer $iThisPostID  The post ID of the subject post. For a new post, 0; for an updating post, a value is assigned.
         */
        private function ___removeTerms( array $aPosts, $sTaxonomySlug, array $aTerms, $iThisPostID ) {
            $_aResults = array();
            if ( empty( $aTerms  ) ) {
                return $_aResults;
            }

            foreach( $aPosts as $_aPost ) {

                $_iPostID = ( integer ) $this->getElement( $_aPost, array( 'ID' ), 0 );
                // Comment: with an ID, New Post: no ID, Update Post: with an ID
                if ( ! $_iPostID && $iThisPostID === $_iPostID ) {
                    $this->_aSavePost = array(
                        $_iPostID => array(
                            'remove_terms' =>  array( $sTaxonomySlug => $aTerms, ),
                        )
                    );
                    $_aResults[] = 'It will be handled in the `save_post_{post type slug}` hook.';
                    continue;
                }
                $_boResult = wp_remove_object_terms(
                    $_iPostID,
                    $aTerms,
                    $sTaxonomySlug
                );
                $_aResults[] = true === $_boResult
                    ? 'Removed term names: ' . implode( ', ', $aTerms ) . ' from Post ID: ' . $_iPostID
                    : $_boResult;
            }
            return $_aResults;

        }
        /**
         * @param WP_Post[] $aPosts
         * @param string $sTaxonomySlug
         * @param array $aTerms
         * @param integer $iThisPostID  The post ID of the subject post. For a new post, 0; for an updating post, a value is assigned.
         */
        private function ___addTerms( array $aPosts, $sTaxonomySlug, array $aTerms, $iThisPostID ) {

            $_aResults = array();
            if ( empty( $aTerms ) ) {
                return $_aResults;
            }
            $_oTaxonomy     = get_taxonomy( $sTaxonomySlug );
            if ( ! $_oTaxonomy ) {
                return $_aResults;
            }
            $_bHierarchical = $_oTaxonomy->hierarchical;
            foreach( $aPosts as $_aPost ) {

                $_iPostID = ( integer ) $this->getElement( $_aPost, array( 'ID' ), 0 );
                // Comment: with an ID, New Post: no ID, Update Post: with an ID
                if ( ! $_iPostID && $iThisPostID === $_iPostID ) {
                    $this->_aSavePost = array(
                        $_iPostID => array(
                            'new_terms' =>  array( $sTaxonomySlug => $aTerms, ),
                        )
                    );
                    $_aResults[] = 'It will be handled in the `save_post_{post type slug}` hook.';
                    continue;
                }
                if ( ! $_bHierarchical ) {
                    $_aResults[] = $this->___getNonHierarchicalTermsAdded( $_iPostID, $sTaxonomySlug, $aTerms );
                    continue;
                }
                $_aThisResults = $this->___getHierarchicalTermsAdded( $_iPostID, $sTaxonomySlug, $aTerms );
                $_aResults     = array_merge( $_aThisResults, $_aResults );

            }
            return $_aResults;

        }
            private function ___getNonHierarchicalTermsAdded( $iPostID, $sTaxonomySlug, $aTerms ) {
                $_aboResult = wp_set_post_terms(
                    $iPostID,
                    $aTerms,            // adding terms
                    $sTaxonomySlug,     // taxonomy slug
                    true         // append
                );
                return is_array( $_aboResult )
                    ? 'Added term IDs: ' . implode( ', ', $_aboResult ) . ' to Post ID: ' . $iPostID
                    : $_aboResult;
            }
            /**
             * @return  array
             */
            private function ___getHierarchicalTermsAdded( $iPostID, $sTaxonomySlug, $aTerms ) {

                $_aResults      = array();
                $_aNewTerms     = array();
                $_aNewTermIDs   = array();
                $_aAddingTerms  = array();
                foreach( $aTerms as $_sTermName ) {
                    $_aniTerm = term_exists( $_sTermName, $sTaxonomySlug );
                    if ( is_null( $_aniTerm ) ) {
                        $_aNewTerms[] = $_sTermName;
                        $_aoResult    = wp_insert_term( $_sTermName, $sTaxonomySlug );
                        if ( is_wp_error( $_aoResult ) ) {
                            $_aResults[]  = $_aoResult;
                            continue;
                        }
                        $_aNewTermIDs[]   = $_aoResult[ 'term_id' ];
                        continue;
                    }
                    $_aAddingTerms[] = is_array( $_aniTerm )
                        ? $_aniTerm[ 'term_id' ]
                        : $_aniTerm;
                }

                // Created new terms
                if ( ! empty( $_aNewTerms ) ) {
                    $_aResults[] = 'Created new hierarchical terms: ' . implode( ', ', $_aNewTerms );
                }

                // Add terms to the post
                $_aAddingTerms = array_merge( $_aAddingTerms, $_aNewTermIDs );
                $_aboResult    = wp_set_post_terms( $iPostID, $_aAddingTerms, $sTaxonomySlug, true );
                $_aResults[]   = is_array( $_aboResult )
                    ? 'Added term IDs: ' . implode( ', ', $_aboResult ) . ' to Post ID: ' . $iPostID
                    : $_aboResult;
                return $_aResults;

            }

    /**
     * Called right after a post is created.
     *
     * @param int     $iPostID          Post ID.
     * @param array   $aSavePostData    Post data array converted version of WP_Post.
     * @param bool    $bUpdate          Whether this is an existing post being updated or not.
     * @return  array   results
     */
    protected function _savePost( $iPostID, array $aSavePostData, $bUpdate ) {

        $_aResults = array();
        $_aPosts   = array( get_post( $iPostID, 'ARRAY_A' ) );

        foreach( $this->getElementAsArray( $aSavePostData, array( 'remove_terms' ) ) as $_sTaxonomySlug => $_aTerms ) {
            $_aResults[] = $this->___removeTerms( $_aPosts, $_sTaxonomySlug, $_aTerms, 0 );
        }
        foreach( $this->getElementAsArray( $aSavePostData, array( 'new_terms' ) ) as $_sTaxonomySlug => $_aTerms ) {
            $_aResults[] = $this->___addTerms( $_aPosts, $_sTaxonomySlug, $_aTerms, 0 );
        }
        foreach( $this->getElementAsArray( $aSavePostData, array( 'remove_all_terms' ) ) as $_sTaxonomySlug => $__sTaxonomySlug ) {
            $_aResults[] = $this->___removeAllTerms( $_aPosts, $_sTaxonomySlug, 0 );
        }
        foreach( $this->getElementAsArray( $aSavePostData, array( 'delete_terms' ) ) as $_sTaxonomySlug => $_aTerms ) {
            $_aResults[] = $this->___deleteTerms( $_aPosts, $_sTaxonomySlug, $_aTerms, 0 );
        }
        foreach( $this->getElementAsArray( $aSavePostData, array( 'delete_all_terms' ) ) as $_sTaxonomySlug => $__sTaxonomySlug ) {
            $_aResults[] = $this->___deleteAllTerms( $_aPosts, $_sTaxonomySlug, 0 );
        }
        return $_aResults;

    }

    /**
     * Used by extended classes. (the $tag and $category directive classes)
     * @param string    $sPostTypeSlug
     * @param boolean   $bHierarchical
     *
     * @return bool|WP_Taxonomy[]
     */
    protected function _getFirstFoundPublicTaxonomy( $sPostTypeSlug, $bHierarchical ) {

        $_aTaxonomies = get_object_taxonomies( $sPostTypeSlug, 'objects' );
        foreach( $_aTaxonomies as $_oTaxonomy ) {
            if ( $bHierarchical && ! $_oTaxonomy->hierarchical ) {
                continue;
            }
            if ( ! $bHierarchical && $_oTaxonomy->hierarchical ) {
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