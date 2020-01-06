<?php
/**
 * Shortcode Directives
 *
 * http://en.michaeluno.jp/shortcode-directivies
 * Copyright (c) 2020 Michael Uno; Licensed under <LICENSE_TYPE>
 */

/**
 * Adds the 'Delete' form section to the 'Reset' tab.
 *
 * @since    0.0.3
 */
class ShortcodeDirectives_AdminPage__FormSection_Delete extends ShortcodeDirectives_AdminPage__FormSection_Base {

    /**
     *
     * @since   0.0.3
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'delete',
            'tab_slug'      => $this->_sTabSlug,
            'title'         => __( 'Delete', 'shortcode-directives' ),
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
                'label'             => __( 'Delete plugin data upon plugin uninstall.', 'shortcode-directives' ),
            )
        );

    }

}