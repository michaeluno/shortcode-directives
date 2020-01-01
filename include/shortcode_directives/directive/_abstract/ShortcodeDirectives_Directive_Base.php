<?php

/**
 * The base class for the classes handling shortcode directives.
 */
abstract class ShortcodeDirectives_Directive_Base extends ShortcodeDirectives_PluginUtility {

    public $sType    = '';  // post / comment

    // @deprecated
    /*public $aTypes   = array(
        'post',
//        'comment'
    );*/

    public $aDirectives = array();

    /**
     * @var mixed
     */
    public $mData = array();

    /**
     * Stores the subject post/comment object.
     * @var WP_Post|WP_Comment
     */
    public $oSubject = 0;

    /**
     * Performs necessary set-ups.
     */
    public function __construct() {
        add_action( 'shortcode_directives_action_register_directives_' . $this->sType, array( $this, 'replyToRegisterShortcode' ), 10, 2 );
        add_action( 'shortcode_directives_action_unregister_directives_' . $this->sType, array( $this, 'replyToUnregisterShortcode' ), 10, 2 );
        add_filter( 'shortcode_directives_filter_directive_names_' . $this->sType, array( $this, 'replyToGetDirectiveNames' ) );
    }

        public function replyToRegisterShortcode( $oSubject, $mData ) {
            $this->oSubject = $oSubject;
            $this->mData = $mData;
            foreach( $this->aDirectives as $_sDirective ) {
                add_shortcode( $_sDirective, array( $this, 'replyToProcessShortCode' ) );
            }
        }
        public function replyToUnregisterShortcode() {
            foreach( $this->aDirectives as $_sDirective ) {
                remove_shortcode( $_sDirective );
            }
        }
        public function replyToGetDirectiveNames( array $aDirectiveNames ) {
            foreach( $this->aDirectives as $_sDirectiveName ) {
                $aDirectiveNames[] = $_sDirectiveName;
            }
            return $aDirectiveNames;
        }

    /**
     * @param $aAttributes
     * @return string
     */
    public function replyToProcessShortCode( $aAttributes, $sContent, $sTagName ){
        $mResult = $this->_doAction(
            $this->getAsArray( $aAttributes ),
            $this->oSubject,
            $this->mData
        );
        do_action( 'shortcode_directives_action_did_directive', $mResult, $sTagName, $aAttributes );
        return '';

    }
//    public function replyToDoAction( $sContent, $sTagName, $aAttributes, $aMatches ) {
//        remove_filter( 'do_shortcode_tag', array( $this, 'replyToDoAction' ), 10 );
//
//        $_sAttributes = $this->getElement( $aMatches, array( 3 ) );
//        $_aAttributes = $this->getAsArray( shortcode_parse_atts( $_sAttributes ) );
//        $mResult = $this->_doAction(
//            $this->getAsArray( $aAttributes ),
//            $this->oSubject,
//            $this->mData
//        );
//
//        do_action( 'shortcode_directives_action_did_directive', $mResult, $sTagName );
//        return $sContent;
//    }

    /**
     * @param array $aAttributes
     * @param object $oSubject
     * @param mixed $mData
     * @return mixed A result code and message.
     */
    protected function _doAction( array $aAttributes, $oSubject, $mData ) {
        return null;
    }


    // maybe deprecated
    protected function _getSubCommands( array $aAttributes ) {
        // Extract values with a numeric key.
        $_aCommands = array();
        foreach( $aAttributes as $_isKey => $_sCommand ) {
            if ( ! is_numeric( $_isKey ) ) {
                continue;
            }
            $_aCommands[] = $_sCommand;
        }
        return $_aCommands;
    }

    /**
     * @param array $aAttributes
     *
     * @return array
     * @deprecated
     */
/*    protected function _getSwitchesExtracted( array &$aAttributes ) {
        $_aSwitches = array();
        foreach( $aAttributes as $_isKey => $_sAttribute ) {
            if ( is_numeric( $_isKey ) && '/' === substr( $_sAttribute, 0, 1 ) ) {
                $_aSwitches[] = strtolower( $_sAttribute );
                unset( $aAttributes[ $_isKey ] );
            }
        }
        return $_aSwitches;
    }*/



    /**
     * @param $sCommaDelimited
     *
     * @return array
     */
    static public function getCommaDelimitedElements( $sCommaDelimited ) {
        if ( ! is_scalar( $sCommaDelimited ) ) {
            return array_filter( ( array ) $sCommaDelimited );
        }
        if ( ! strlen( $sCommaDelimited ) ) {
            return array();
        }
        $_aArray = preg_split( "/[,]\s*/", trim( $sCommaDelimited ), 0, PREG_SPLIT_NO_EMPTY );
        return array_filter( $_aArray );
    }

    /**
     * @see https://stackoverflow.com/a/11040612
     * @param array $aNeedles
     * @param $aHaystack
     *
     * @return bool
     * @deprecated
     */
    static public function isInArrayAny( array $aNeedles, $aHaystack ) {
        return ! empty( array_intersect( $aNeedles, $aHaystack ) );
    }


    static public function getElementsOfAssociativeKeys( array $aArray ) {
        foreach( $aArray as $_isKey => $_mValue ) {
            if ( is_numeric( $_isKey ) ) {
                unset( $aArray[ $_isKey ] );
            }
        }
        return $aArray;
    }

    static public function getElementsOfNumericKeys( array $aArray ) {
        foreach( $aArray as $_isKey => $_mValue ) {
            if ( ! is_numeric( $_isKey ) ) {
                unset( $aArray[ $_isKey ] );
            }
        }
        return $aArray;
    }

    /**
     * @see   https://wordpress.stackexchange.com/a/81648
     * @param $iPostID
     *
     * @return array
     */
    static public function getDescendants( $iPostID ){

        $_aChildren = array();

        // grab the posts children
        $_oPost     = get_post( $iPostID );
        $_aPosts    = get_posts(
            array( 
                'numberposts'       => -1,
                'post_parent'       => $iPostID,
                'post_type'         => $_oPost->post_type,
                'suppress_filters'  => false 
            )
        );

        // now grab the grand children
        foreach( $_aPosts as $_oChildPost ) {

            // recursion!! hurrah
            $_aGrandChildren = self::getDescendants( $_oChildPost->ID );

            // merge the grand children into the children array
            if ( ! empty( $_aGrandChildren ) ) {
                $_aChildren = array_merge( $_aChildren, $_aGrandChildren );
            }

        }

        // merge in the direct descendants we found earlier
        return array_merge( $_aChildren, $_aPosts );

    }

}