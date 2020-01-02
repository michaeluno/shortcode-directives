<?php
/**
 *
 */

/**
 * Provides utility methods for the directives component.
 */
class ShortcodeDirectives_Directive_Utility extends ShortcodeDirectives_PluginUtility {

    /**
     * Generates a array representation in a single line used for a log entry.
     * @param array $aArray
     * @return string
     */
    static public function getArrayRepresentation( array $aArray ) {
        $_sOutput = '';
        foreach( $aArray as $_sKey => $_mValue ) {
            $_sOutput .= is_array( $_mValue )
                ? "$_sKey: (" . self::getArrayRepresentation( $_mValue ) . ') '
                : "$_sKey: " . $_mValue . ' ';
        }
        return rtrim( $_sOutput );
    }

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