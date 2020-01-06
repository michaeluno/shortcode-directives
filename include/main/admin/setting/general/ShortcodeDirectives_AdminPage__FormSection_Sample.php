<?php
/**
 * Shortcode Directives
 *
 * http://en.michaeluno.jp/shortcode-directivies
 * Copyright (c) 2020 Michael Uno; Licensed under <LICENSE_TYPE>
 */

/**
 * Adds the 'Sample' form section to the 'General' tab.
 *
 * @since    0.0.3
 */
class ShortcodeDirectives_AdminPage__FormSection_Sample extends ShortcodeDirectives_AdminPage__FormSection_Base {

    /**
     *
     * @since   0.0.3
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'sample',
            'tab_slug'      => $this->_sTabSlug,
            'title'         => __( 'Sample', 'shortcode-directives' ),
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
                'field_id'          => 'delete_on_uninstall',
                'type'              => 'checkbox',
                'show_title_column' => false,
                'label'             => __( 'This is a checkbox', 'shortcode-directives' ),
            )
        );

    }

}