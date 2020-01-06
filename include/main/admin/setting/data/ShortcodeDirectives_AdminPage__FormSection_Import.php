<?php
/**
 * Shortcode Directives
 *
 * http://en.michaeluno.jp/shortcode-directivies
 * Copyright (c) 2020 Michael Uno; Licensed under <LICENSE_TYPE>
 */

/**
 * Adds the 'Import' form section to the 'Manage Options' tab.
 *
 * @since    0.0.3
 */
class ShortcodeDirectives_AdminPage__FormSection_Import extends ShortcodeDirectives_AdminPage__FormSection_Base {

    /**
     *
     * @since   0.0.3
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'import',
            'tab_slug'      => $this->_sTabSlug,
            'title'         => __( 'Import', 'shortcode-directives' ),
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
                'field_id'          => 'import_options',
                'title'             => __( 'Import Options', 'shortcode-directives' ),
                'type'              => 'import',
                'value'             => __( 'Upload Options', 'shortcode-directives' ),
                'save'              => false,
            )
        );

    }

}