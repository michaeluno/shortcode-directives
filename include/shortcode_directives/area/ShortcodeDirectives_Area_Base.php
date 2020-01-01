<?php


abstract class ShortcodeDirectives_Area_Base extends ShortcodeDirectives_PluginUtility {

    public $aPostTypeSlugs = array();

    /**
     * Performs necessary set-ups.
     */
    public function __construct( $asPostTypeSlug ) {
        $this->aPostTypeSlugs = ( array ) $asPostTypeSlug;
        $this->_construct();
    }

    protected function _construct() {}

    protected function _getDirectiveNames( $sType ) {
        $_aDirectives = apply_filters( 'shortcode_directives_filter_directive_names_' . $sType, array() );
        return array_unique( $_aDirectives );
    }

    /**
     * `do_shortcode()` alternative in order to avoid to execute any unintended third-party shortcodes.
     * @see     do_shortcode()
     * @param   string $sContent
     * @return  string
     */
    protected function _processShortcode( $sContent, array $aShortCodeTags ) {

        preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $sContent, $_aMatches );
        $aShortCodeTags = array_intersect( $aShortCodeTags, $_aMatches[ 1 ] );
        if ( empty( $aShortCodeTags ) ) {
            return $sContent;
        }
        $sContent       = do_shortcodes_in_html_tags( $sContent, false, $aShortCodeTags );
        $_sPattern      = get_shortcode_regex( $aShortCodeTags );
        $sContent       = preg_replace_callback( "/$_sPattern/", 'do_shortcode_tag', $sContent );
        return unescape_invalid_shortcodes( $sContent );

    }

    /**
     * Checks if the user is capable of performing the directive.
     * @param $iUserID
     * @param WP_Post|array $aoPost
     *
     * @return bool
     */
    protected function _isAuthorizedUser( $iUserID, $aoPost ) {

        $_sPostType         = is_array( $aoPost )
            ? $this->getElement( $aoPost, array( 'post_type' ) )
            : $aoPost->post_type;
        $_iPostAuthor       = is_array( $aoPost )
            ? ( integer ) $this->getElement( $aoPost, array( 'post_type' ), 0 )
            : ( integer ) $aoPost->post_author;
        $_oOption           = ShortcodeDirectives_Option::getInstance();
        $_aPermissions      = $this->getAsArray( $_oOption->get( array( 'post_types', $_sPostType, 'permissions' ) ) );
        $_bAuthorAllowed    = $this->getElement( $_aPermissions, array( 'author' ), false );
        if ( $_bAuthorAllowed && $_iPostAuthor === $iUserID ) {
            return true;
        }

        $_aAllowedUserRoles = $this->getElementAsArray( $_aPermissions, array( 'user_roles' ), array() );
        $_aAllowedUserRoles = array_keys( array_filter( $_aAllowedUserRoles ) );
        $_oUser             = get_user_by( 'ID', $iUserID );

        if ( empty( array_intersect( $_aAllowedUserRoles, $_oUser->roles ) ) ) {
            return false;
        }
        return true;

    }

}