<?php

/**
 * The base class for the classes handling shortcode directives.
 */
abstract class ShortcodeDirectives_Directive_Base extends ShortcodeDirectives_Directive_Utility {

    /**
     * Contains the supported types.
     * Accepts `post` or `comment`.
     * Override this property in an extended class.
     * @var array
     */
    public $aTypes   = array(
        'post',
//        'comment'
    );

    public $aOptions = array(
        // Specifies the target to apply the diected operation
        // multiple values can be set separated by commas
        '--to'     => 'self',     // self, parent, children, descendants, {post id}
    );

    /**
     * Stores the shortcode directive tag names.
     * Override this property in an extended class.
     * @var array
     */
    public $aDirectives = array(
        // '$my-directive'
    );

    public $aSubCommands = array(
        // 'draft' // e.g. $post_status draft
    );

    /**
     * @var mixed
     */
    public $mData = array();

    /**
     * Stores the subject post/comment data, usually a post array converted from WP_Post.
     * The reason that WP_Post object is not used is because for the `wp_insert_post_data` filter hook,
     * for a new post, a post is not created yet so it does not have an ID yet. Thus, not possible to form as a WP_Post object.
     * @var array
     */
    public $aSubject;

    /**
     * Stores supported pot type slugs for the post area.
     * Used to add callbackes for the `wp_insert_post_{post type slug}` hook.
     * @var array
     */
    public $aPostTypeSlugs = array();

    /**
     * Stores currently processed type.
     * Supports `post` or `comment`.
     * @var string
     */
    protected $_sCurrentType = '';

    /**
     * Performs necessary set-ups.
     */
    public function __construct( array $aPostTypeSlugs ) {
        $this->aPostTypeSlugs = $aPostTypeSlugs;
        foreach( $this->aTypes as $_sType ) {
            add_action( 'shortcode_directives_action_register_directives_' . $_sType, array( $this, 'replyToRegisterShortcode' ), 10, 2 );
            add_action( 'shortcode_directives_action_unregister_directives_' . $_sType, array( $this, 'replyToUnregisterShortcode' ), 10, 2 );
            add_filter( 'shortcode_directives_filter_directive_names_' . $_sType, array( $this, 'replyToGetDirectiveNames' ) );
        }
    }
        public function replyToRegisterShortcode( $aSubject, $mData ) {

            $this->_sCurrentType = str_replace( 'shortcode_directives_action_register_directives_', '', current_action() );
            $this->aSubject      = $aSubject;
            $this->mData         = $mData;
            foreach( $this->aDirectives as $_sDirective ) {
                add_shortcode( $_sDirective, array( $this, 'replyToDoShortCode' ) );
            }

            // Case: the `post` type is supported.
            if ( ! in_array( 'post', $this->aTypes, true ) ) {
                return;
            }
            add_filter( 'shortcode_directives_filter_insert_post_data', array( $this, 'replyToModifyInsertPostData' ), 10 );
            foreach( $this->aPostTypeSlugs as $_sPostTypeSlug ) {
                add_action( 'save_post_' . $_sPostTypeSlug, array( $this, 'replyToSavePost' ), 10, 3 );
            }

        }
        public function replyToUnregisterShortcode() {
            foreach( $this->aDirectives as $_sDirective ) {
                remove_shortcode( $_sDirective );
            }
            if ( ! in_array( 'post', $this->aTypes, true ) ) {
                return;
            }
            remove_filter( 'shortcode_directives_filter_insert_post_data', array( $this, 'replyToModifyInsertPostData' ), 10 );

            // Not removing the save_post_{post type slug} callbacks because this method is called each time the short code detection routine is triggered
            // if these callbacksa are removed, the save post callbacks will never be called.
//            foreach( $this->aPostTypeSlugs as $_sPostTypeSlug ) {
//                remove_action( 'save_post_' . $_sPostTypeSlug, array( $this, 'replyToSavePost' ), 10 );
//            }
        }
        public function replyToGetDirectiveNames( array $aDirectiveNames ) {
            foreach( $this->aDirectives as $_sDirectiveName ) {
                $aDirectiveNames[] = $_sDirectiveName;
            }
            return $aDirectiveNames;
        }

    /**
     * Stores arbitrary data which are referred with the `shortcode_directives_filter_insert_post_data` filter hook.
     *
     */
    protected $_aInsertPost = array();

    /**
     * Stores arbitrary data which are referred with the `save_post_{post type}` action hook.
     * If not empty, the `_savePost()` method will be called.
     * @var array
     */
    protected $_aSavePost = array();

    /**
     *
     * @remark  For the comment type, this is not called.
     * @param integer  $iPostID
     * @param WP_Post $oPost
     * @param boolean $bUpdate
     * @callback action save_post_{post type slug}
     * @see wp_insert_post()
     */
    public function replyToSavePost( $iPostID, WP_Post $oPost, $bUpdate ) {

        if ( ! $iPostID ) {
            return;
        }
        if ( empty( $this->_aSavePost ) ) {
            return;
        }
        $_iIndex   = $bUpdate ? $iPostID : 0;
        $_aData    = $this->getElementAsArray( $this->_aSavePost, array( $_iIndex ) );
        $_aResults = $this->_savePost( $iPostID, $_aData, $bUpdate );
        if ( ! empty( $_aResults ) ) {
            do_action( 'shortcode_directives_action_saved_post', $_aResults );
        }

    }
        /**
         * @remark  For the comment type, this is not called.
         * @param   integer     $iPostID
         * @param   array       $aSavePostData
         * @param   boolean     $bUpdate
         * @return  array       Results
         */
        protected function _savePost( $iPostID, array $aSavePostData, $bUpdate ) {
            return array();
        }

    /**
     * Called when a new post is going to be created which does not have a post ID yet, or a post is updated with an existent post ID.
     * Called right after the shortcode directive is processed.
     * This method changes the inserting post data such as post statuses, post parent etc.
     * @remark      For the comment type, this is not called.
     * @param       array $aPostData
     * @return      array
     * @callback    filter  shortcode_directives_filter_insert_post_data
     */
    public function replyToModifyInsertPostData( array $aPostData ) {
        if ( empty( $this->_aInsertPost ) ) {
            return $aPostData;
        }
        return $this->_getPostDataToInsert( $aPostData, $this->_aInsertPost );
    }
        /**
         * @remark  For the comment type, this is not called.
         * @param   array $aInsertPost  The post data to be inserted to the post table.
         * @param   array $aModifyPost  The data to be used for modifying the inserting post data. It is assumed this data is created duroing the `_doAction()` method.
         * @return  array
         */
        protected function _getPostDataToInsert( array $aInsertPost, array $aModifyPost ) {
            return $aModifyPost + $aInsertPost;
        }

    /**
     * @param $aAttributes
     * @return string   The output for the shortcode.
     */
    public function replyToDoShortCode( $aAttributes, $sContent, $sTagName ){
        add_filter( 'do_shortcode_tag', array( $this, 'replyToDoAction' ), 10, 4 );
        return '';
    }
        public function replyToDoAction( $sContent, $sTagName, $aAttributes, $aMatches ) {

            // This callback is volatile
            remove_filter( 'do_shortcode_tag', array( $this, 'replyToDoAction' ), 10 );

            // Prevent recursive calls within the `wp_insert_post_data` filter hook with wp_update_post() -> wp_insert_post().
            do_action( 'shortcode_directives_action_unregister_hooks_of_area_posts' );
            do_action( 'shortcode_directives_action_unregister_directives_post', $this->aSubject, $this->mData );

            // Performs the directive
            $this->_initialize();
            $mResult      = $this->_doAction(
                $this->getAsArray( $aAttributes ),
                $this->aSubject,
                $this->mData
            );

            // Recover the hooks
            do_action( 'shortcode_directives_action_register_hooks_of_area_posts' );
            do_action( 'shortcode_directives_action_register_directives_post', $this->aSubject, $this->mData );

            // Log
            $_sAttributes = $this->getElement( $aMatches, array( 3 ) );
            do_action( 'shortcode_directives_action_did_directive', $mResult, $sTagName, $_sAttributes );
            return $sContent;

        }

    /**
     * Initializes properties.
     *
     * The _doAction() method can be called multiple times in a single page load.
     * A first call can affect a second call with modified properties. To prevent that,
     * reset the properties each time before the `_doAction()` method is called.
     *
     * This method is called right before the _doAction() method.
     * Override this method to initialize properties.
     */
    protected function _initialize() {
        $this->_aInsertPost = array();
        $this->_aSavePost   = array();
    }

    /**
     * @param array $aAttributes
     * @param array $aSubject
     * @param mixed $mData
     * @return string|array|WP_Error A result code and message to be logged.
     */
    protected function _doAction( array $aAttributes, $aSubject, $mData ) {
        return '';
    }

    /**
     * Extracts command options.
     * The option names must start with `--` such as `--self`
     * @param array $aAttributes
     * @return array
     */
    protected function _getCommandOptions( array $aAttributes ) {
        foreach( $aAttributes as $_sKey => $_mValue ) {
            if ( '--' !== substr( $_sKey, 0, 2 ) ) {
                unset( $aAttributes[ $_sKey ] );
            }
        }
        return $aAttributes + $this->aOptions;
    }

    /**
     * Extracts sub-commands (associative array keys).
     *
     * e.g.
     * array( 'key1', 'key2' )
     *
     * @param array $aAttributes
     * @param array $aSubCommands   A numerically indexed array holding sub-command names.
     * @return array
     */
    protected function _getSubCommands( array $aAttributes, array $aSubCommands ) {
        $_aNumericKeyValues = $this->getElementsOfNumericKeys( $aAttributes );
        if ( empty( $aSubCommands ) ) {
            return array_values( $_aNumericKeyValues );
        }
        return array_values( array_intersect( $_aNumericKeyValues, $aSubCommands ) );
    }

    /**
     * @param array $aPost
     * @param integer|string $isTo Specifies which post to be handled by the directive operation.
     * @param string $sDefault The default target type. Accepts, self, parent, children, siblings, descendants
     * @return array    An array with index of post ID, holding a post array in each element
     */
    protected function _getTargetPosts( array $aPost, $isTo, $sDefault='self' ) {

        $_aTo       = $this->getCommaDelimitedElements( $isTo );
        if ( empty( $_aTo ) ) {
            $_aTo = array( $sDefault );
        }

        $_aAllPosts = array();
        foreach( $_aTo as $_isTo ) {

            // Case: to={post id} e.g. to=3455
            if ( is_numeric( $_isTo ) ) {
                $_aPost   = get_post( $_isTo, 'ARRAY_A' );
                $_iPostID = $this->getElement( $_aPost, array( 'ID' ), 0 );
                $_aPosts  = empty( $_aPost )
                    ? array()
                    : array(
                        $_iPostID => $_aPost
                    );
                $_aAllPosts = $_aPosts + $_aAllPosts;
                continue;
            }
            $_sMethodName   = '___getPostArraysOf_' . $_isTo;
            if ( method_exists( $this, $_sMethodName ) ) {
                $_aPosts    = $this->{$_sMethodName}( $aPost );
                $_aAllPosts = $_aPosts + $_aAllPosts;
                continue;
            }
            $_sMethodName   = '___getPostArraysOf_' . $sDefault;
            $_aPosts        = method_exists( $this, $_sMethodName )
                ? $this->{$_sMethodName}( $aPost )
                : $this->___getPostArraysOf_self( $aPost );
            $_aAllPosts = $_aPosts + $_aAllPosts;
        }

        return $_aAllPosts;

    }
        private function ___getPostArraysOf_self( $aPost ) {
            $_iPostID = $this->getElement( $aPost, array( 'ID' ), 0 );
            return array( $_iPostID => $aPost );
        }
        private function ___getPostArraysOf_parent( $aPost ) {
            $_aParent   = get_post( $aPost[ 'post_parent' ], 'ARRAY_A' );
            $_iParentID = $this->getElement( $_aParent, array( 'ID' ), 0 );
            return empty( $_aParent )
                ? array()
                : array( $_iParentID => $_aParent );
        }
        private function ___getPostArraysOf_children( $aPost ) {
            $_iPostID = ( integer ) $this->getElement( $aPost, array( 'ID' ), 0 );
            if ( ! $_iPostID ) {
                return array();
            }
            $_oQuery  = new WP_Query();
            $_aPosts  = $_oQuery->query(
                array(
                    'posts_per_page' => -1,
                    'post_parent'    => $_iPostID,
                    'post_type'      => $aPost[ 'post_type' ],
                )
            );
            return $this->___getWPPostsToArrays( $_aPosts );
        }
        private function ___getPostArraysOf_siblings( $aPost ) {
            $_iParentID = $this->getElement( $aPost, array( 'post_parent' ), 0 );
            $_aPosts    = $this->___getPostArraysOf_parent( $aPost );
            $_aParent   = $this->getElementAsArray( $_aPosts, array( $_iParentID ) );
            return $this->___getPostArraysOf_children( $_aParent );
        }
        private function ___getPostArraysOf_descendants( $aPost ) {
            $_iPostID = ( integer ) $this->getElement( $aPost, array( 'ID' ), 0 );
            if ( ! $_iPostID ) {
                return array();
            }
            $_aPosts = $this->getDescendants( $_iPostID );
            return $this->___getWPPostsToArrays( $_aPosts );
        }
            /**
             * Converts WP_Post[] to array[] holding converted post arrays.
             *
             * Not using WP_Post objects for compatibility with a `wp_insert_post_data` filter callback that cannot have a WP_Post object.
             * @param array $aPosts
             *
             * @return array
             */
            private function ___getWPPostsToArrays( array $aPosts ) {
                $_aPosts = array();
                foreach( $aPosts as $_oPost ) {
                    if ( $_oPost instanceof WP_Post ) {
                        $_iPostID  = $_oPost->ID;
                        $_aPosts[ $_iPostID ] = get_post( $_iPostID, 'ARRAY_A' );
                    }
                }
                return $_aPosts;
            }

}