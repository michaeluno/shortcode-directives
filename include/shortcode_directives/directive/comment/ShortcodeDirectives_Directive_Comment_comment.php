<?php

/**
 * Applies some actions to the subject comment.
 *
 * For a normal usage, reply to the subject comment with a sub-command to perform certain action against the subject comment.
 * When a directive is submitted agains a _post_, (not a comment), the action will be applied to all the comments belonging to the subject post.
 *
 * Directive: `$comment`
 * Sub-commands:
 *  - delete|remove: deletes the comment
 *  - hold|disapprove: changes the comment status to `hold`.
 *  - trash: moves the comment to trash
 *  - spam: mark the commet as spam
 *  - convert: converts the comment to a child post.
 *
 * Usage: [$comment {sub-command}]
 * Examples:
 * ```
 * [$comment delete]
 * ```
 * ```
 * [$comment hold]
 * ```
 *
 */
class ShortcodeDirectives_Directive_Comment_comment extends ShortcodeDirectives_Directive_Base {

    public $aTypes   = array(
//        'post',
        'comment'
    );

    public $aDirectives = array(
        '$comment',
//        '$comment_status',
//        '$comment-status',
    );

    public $aSubCommands = array( 
        'delete', 'remove', 'hold', 'disapprove', 'trash', 'spam',
        'convert',
    );

    public $aOptions = array();

    /**
     * @param   array $aAttributes
     * @param   array $aSubject
     * @param   array $aData
     * @return  string|WP_Error|WP_Error[]|array
     */
    protected function _doAction( array $aAttributes, $aSubject, $aCommentData ) {

        $aAttributes  = $aAttributes + $this->aOptions;
        $aCommentData = $aCommentData + array( 'comment_parent' => 0, 'comment_ID' => 0 );
        $_iThisPostID = $this->getElement( $aSubject, array( 'ID' ), 0 );
        if ( ! $aCommentData[ 'comment_parent' ] && ! $_iThisPostID ) {
            return 'A post ID could not be detected. A parent comment does not exist.';
        }
        $_aComments   = $this->___getCommentsToParse( $aCommentData, $_iThisPostID );
        $_aCommands   = $this->_getSubCommands( $aAttributes, $this->aSubCommands );

        $_aResults    = array();
        foreach( $_aComments as $_oComment ) {
            $_aosResult = $this->___getEachCommentParsed( $_oComment, $_aCommands );
            if ( is_array( $_aosResult ) ) {
                $_aResults = array_merge( $_aResults, $_aosResult );
                continue;
            }
            $_aResults[] = $_aosResult;
        }
        return $_aResults;

    }

        private function ___getEachCommentParsed( WP_Comment $oComment, $_aCommands ) {

            $_iCommentID = $oComment->comment_ID;
            if ( ! empty( array_intersect( array( 'remove', 'delete' ), $_aCommands ) ) ) {
                $_bResult = wp_delete_comment( $_iCommentID, true );
                return $_bResult
                    ? 'Deleted the comment, ID: ' . $_iCommentID
                    : 'Failed to delete the comment, ID: ' . $_iCommentID;
            }
            if ( ! empty( array_intersect( array( 'hold', 'disapprove' ), $_aCommands ) ) ) {
                $_boResult = wp_set_comment_status( $_iCommentID, 'hold', false );
                return true === $_boResult
                    ? 'Changed the comment status of comment ID: ' . $_iCommentID . ' to hold'
                    : ( is_wp_error( $_boResult )
                        ? $_boResult
                        : 'Faield to change the comment status of comment ID: ' . $_iCommentID . ' to hold'
                    );
            }
            if ( in_array( 'trash', $_aCommands, true ) ) {
                $_bResult = wp_trash_comment( $_iCommentID );
                return $_bResult
                    ? 'Move the comment to trash. Comment ID: ' . $_iCommentID
                    : 'Failed to move the comment to trash. Comment ID: ' . $_iCommentID;
            }
            if ( in_array( 'spam', $_aCommands, true ) ) {
                $_boResult = wp_set_comment_status( $_iCommentID, 'spam', false );
                return true === $_boResult
                    ? 'Changed the comment status of comment ID: ' . $_iCommentID . ' to spam'
                    : ( is_wp_error( $_boResult )
                        ? $_boResult
                        : 'Faield to change the comment status of comment ID: ' . $_iCommentID . ' to spam'
                    );
            }

            if ( in_array( 'convert', $_aCommands, true ) ) {
                $_oComment = get_comment( $_iCommentID );
                if ( ! ( $_oComment instanceof WP_Comment ) ) {
                    return 'Failed to create a comment object. Comment ID: ' . $_iCommentID;
                }
                return $this->___getCommentsConvertedToPosts( $_oComment );
            }
            return 'No available command is detected.';

        }

        private function ___getCommentsToParse( $aCommentData, $iPostID ) {
            if ( ! $aCommentData[ 'comment_parent' ] ) {
                // retrieving only direct children
                return get_comments(
                    array(
                        'post_id'   => $iPostID,
                        'parent'    => 0,   // comment parent
                    )
                );
            }
            return array( get_comment( $aCommentData[ 'comment_parent' ] ) );
        }


        /**
         * @param WP_Comment $oComment
         * @param int $iTargetPostID
         *
         * @return array
         */
        private function ___getCommentsConvertedToPosts( WP_Comment $oComment, $iTargetPostID=0 ) {

            $_iTargetPostID  = $iTargetPostID ? $iTargetPostID : $oComment->comment_post_ID;
            $_aResults       = array();
            $_ioPostID       = $this->___getCommentConvertedToPost( $oComment, $_iTargetPostID );
            if ( is_wp_error( $_ioPostID ) ) {
                $_ioPostID->add( 'post_creation_failed', 'Failed to create a post. Comment ID: ' . $oComment->comment_ID );
                $_aResults[] = $_ioPostID;
                return $_aResults;
            }
            $_aResults[]     = "Converted a comment {$oComment->comment_ID} to a post {$_ioPostID}";
            foreach( $oComment->get_children() as $_oChildComment ) {
                $_aResults   = array_merge( $_aResults, $this->___getCommentsConvertedToPosts( $_oChildComment, $_ioPostID ) );
            }
            wp_delete_comment( $oComment, true );
            return $_aResults;
            
        }
            /**
             * @param WP_Comment $oComment
             * @param $iPostParentID
             *
             * @return int|WP_Error
             */
            private function ___getCommentConvertedToPost( WP_Comment $oComment, $iPostParentID ) {

                $_oParrent = get_post( $iPostParentID );
                if ( ! ( $_oParrent instanceof WP_Post ) ) {
                    return new WP_Error( 'post_creation_failed', 'Cannot create a post object with the post ID: ' . $iPostParentID );
                }
                return wp_insert_post(
                    array(
                        'post_type'     => $_oParrent->post_type,
                        'post_parent'   => $iPostParentID,
                        'post_title'    => function_exists( 'mb_substr' )
                            ? mb_substr( strip_tags( $oComment->comment_content ), 0, 50 )
                            : substr( strip_tags( $oComment->comment_content ), 0, 50 ),
                        'post_content'  => $oComment->comment_content,
                        'post_author'   => $oComment->user_id,
                        'post_status'   => $this->___getPostStatusConvertedFromCommentStatus( $oComment->comment_approved ),
                        'post_date'     => $oComment->comment_date,
                        'post_date_gmt' => $oComment->comment_date_gmt,
                    )
                );

            }

                private function ___getPostStatusConvertedFromCommentStatus( $sCommentStatus ) {

                    if ( ! $sCommentStatus ) {
                        return 'pending';
                    }
                    if ( in_array( $sCommentStatus, array( 'trash', 'spam' ), true ) ) {
                        return 'trash';
                    }
                    return 'publish';

                }

}