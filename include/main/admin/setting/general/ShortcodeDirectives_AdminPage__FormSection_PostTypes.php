<?php
/**
 * Shortcode Directives
 *
 * http://en.michaeluno.jp/shortcode-directivies
 * Copyright (c) 2020 Michael Uno; Licensed under <LICENSE_TYPE>
 */

/**
 * Adds the 'Post Types' form section to the 'General' tab.
 *
 * @since    0.0.3
 */
class ShortcodeDirectives_AdminPage__FormSection_PostTypes extends ShortcodeDirectives_AdminPage__FormSection_Base {

    /**
     *
     * @since   0.0.3
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'post_types',
            'tab_slug'      => $this->_sTabSlug,
            'title'         => __( 'Post Types', 'shortcode-directives' ),
        );
    }

    /**
     * Get adding form fields.
     * @since    0.0.3
     * @return   array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array(
                'field_id'          => 'supported',
                'type'              => 'checkbox',
                'show_title_column' => false,
                'label'             => $this->___getPostTypeLabels(),
                'description'       => __( 'Check post types to support shortcode detectives.', 'shortcode-detectives' ),
            )
        );

    }
        private function ___getPostTypeLabels() {
            $_aArguments = array(
                'public'   => true,
//                '_builtin' => false
            );
            $_aPostTypes = get_post_types( $_aArguments, 'objects' );
            $_aLabels    = array();
            foreach( $_aPostTypes as $_oPostType ) {
                $_aLabels[ $_oPostType->name ] = $_oPostType->labels->name;
            }
            return $_aLabels;
        }

}