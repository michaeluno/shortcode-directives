<?php
/**
 * Shortcode Directives
 *
 * [PROGRAM_URI]
 * Copyright (c) 2020 Michael Uno; Licensed under <LICENSE_TYPE>
 */

/**
 * Adds the 'Items' form section to the 'Items' tab.
 *
 * @since   0.0.3
 * @extends ShortcodeDirectives_AdminPage__FormSection_Base
 */
class ShortcodeDirectives_AdminPage__FormSection_PostType extends ShortcodeDirectives_AdminPage__FormSection_Base {

    protected function _construct( $oFactory ) {
//        add_filter(
//            "options_" . $oFactory->oProp->sClassName,
//            array( $this, 'replyToSetOptions' )
//        );
    }
        public function replyToSetOptions( $aOptions ) {

            $aOptions[ 'post_types' ] = $this->uniteArrays(
                $this->___getPostTypeLabels(),
                $aOptions[ 'post_types' ]
            );
            return $aOptions;
        }

        private function ___getPostTypeLabels() {
            $_aPostTypes = $this->___getPostTypes();
            $_aLabels    = array();
            foreach( $_aPostTypes as $_oPostType ) {
                $_aLabels[ $_oPostType->name ] = array( 'name' => $_oPostType->labels->name );
            }
            return $_aLabels;
        }

    /**
     *
     * @since   0.0.3
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'post_types',
            'title'         => __( 'Supported Post Types', 'shortcode-directives' ),
            'description'   => array(
                __( 'Choose post types that enable shortcode directives.', 'shortcode-directives' ),
            ),
//            'collapsible'       => array(
//                'toggle_all_button' => array( 'top-left', 'bottom-left' ),
//                'container'         => 'section',
//                'is_collapsed'      => false,
//            ),
//            'repeatable'        => true, // this makes the section repeatable
//            'sortable'          => true,
            'content'   => $this->___getPostTypeSections(),

        );
    }
        private function ___getPostTypeSections() {
            $_aPostTypes = $this->___getPostTypes();
            $_aSections  = array();
            $_iIndex     = 0;
            $_iMaxIndex  = count( $_aPostTypes ) - 1;
            foreach( $_aPostTypes as $_oPostType ) {
                $_aSection    = array(
                    'section_id'    => $_oPostType->name,
                    'title'         => $_oPostType->labels->name,
                    'description'   => $_oPostType->description,
                    'collapsible'   => array(
//                        'container'         => 'section',
                        'toggle_all_button' => ! $_iIndex
                            ? 'top-right'
                            :  ( $_iMaxIndex === $_iIndex
                                ? 'bottom-right'
                                : '' ),
                    ),
                );
                $_aSections[ $_iIndex ] = $_aSection;
                $_iIndex++;
            }
            return $_aSections;

        }
    /**
     * Get adding form fields.
     * @since    0.0.3
     * @return   array
     */
    protected function _getFields( $oFactory ) {
        $_aPostTypes = $this->___getPostTypes();
        $_aFields    = array();
        foreach( $_aPostTypes as $_oPostType ) {
            $_aFields = array_merge(
                $_aFields,
                $this->___getFieldsets( array( $this->_sSectionID, $_oPostType->name ), $_oPostType )
            );
        }
        return $_aFields;
    }
        private function ___getFieldsets( $asSectionID, WP_Post_Type $oPostType ) {
            return array(
                array(
                    'section_id'        => $asSectionID,
                    'field_id'          => '_post_type_description',
                    'content'           => $oPostType->description,
                    'if'                => $oPostType->description,
                    'save'              => false,
                    'show_title_column' => false,
                ),
                array(
                    'section_id'       => $asSectionID,
                    'field_id'         => 'areas',
                    'type'             => 'checkbox',
                    'title'            => __( 'Areas', 'shortcode-directives' ),
                    'label'            => array(
                        'post'      => __( 'Posts', 'shortcode-directives' ),
                        'comment'   => __( 'Comments', 'shortcode-directives' ),
                    ),
                ),
                array(
                    'section_id'       => $asSectionID,
                    'field_id'         => 'permissions',
//                    'type'             => 'checkbox',
                    'title'            => __( 'Permissions', 'shortcode-directives' ),
                    'content'          => array(
                        array(
                            'field_id'         => 'user_roles',
                            'title'            => __( 'User Roles', 'shortcode-directives' ),
                            'type'             => 'checkbox',
                            'label'            => $this->___getUserRoleLabels(),
                        ),
                        array(
                            'field_id'         => 'author',
                            'type'             => 'checkbox',
                            'title'            => __( 'Author', 'shortcode-directives' ),
                            'label'            => __( 'Check this to give a permission to perform directives against own posts/comments to the author.', 'shortcode-directives' ),
                        ),
                    ),


                        // __( 'Allow the author to perform the directive.', 'shortcode-directives' ),
                )
            );
        }
            private function ___getUserRoleLabels() {
                $_aRoles = $this->getAsArray( $GLOBALS[ 'wp_roles' ]->get_names() );
                unset( $_aRoles[ 'author' ], $_aRoles[ 'subscriber' ], $_aRoles[ 'contributor' ] );
                return $_aRoles;
            }

    private $___aCaches = array();
    private function ___getPostTypes() {
        if ( isset( $this->___aCaches[ 'post_types' ] ) ) {
            return $this->___aCaches[ 'post_types' ];
        }
        $_aArguments = array(
            'public'   => true,
            //                '_builtin' => false
        );
        $_aPostTypes = get_post_types( $_aArguments, 'objects' );
        $this->___aCaches[ 'post_types' ] = $_aPostTypes;
        return $this->___aCaches[ 'post_types' ];
    }

}