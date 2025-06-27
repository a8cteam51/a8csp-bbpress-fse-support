<?php
/**
 * The a8csp-bbpress-fse-support bootstrap file.
 *
 * @since       1.0.0
 * @version     1.0.0
 * @package     A8C\SpecialProjects\Plugins
 * @author      WordPress.com Special Projects
 * @license     GPL-3.0-or-later
 *
 * @noinspection    ALL
 *
 * @wordpress-plugin
 * Plugin Name:             a8csp-bbpress-fse-support
 * Plugin URI:              https://specialprojects.automattic.com
 * Description:             This plugin adds support for the Block Editor in bbPress.
 * Version:                 1.0.0
 * Requires at least:       6.7
 * Tested up to:            6.7
 * Requires PHP:            8.3
 * Author:                  WordPress.com Special Projects
 * Author URI:              https://specialprojects.automattic.com
 * License:                 GPL v3 or later
 * License URI:             https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:             a8csp-bbpress-fse-support
 * Domain Path:             /languages
 **/

defined( 'ABSPATH' ) || exit;

// If bbPress is not installed, exit.
if ( ! class_exists( 'bbPress' ) ) {
	return;
}

/**
 * Add bbPress theme support for Full Site Editing
 *
 * @param string $template Template to include.
 *
 * @return string
 */
function a8csp_bbpress_fse_support_theme_support( string $template ): string {

	if ( is_buddypress() || ! is_bbpress() ) {
		return $template;
	}

	global $_wp_current_template_content;
	ob_start();
	?>
	<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->

	<!-- wp:group {"tagName":"main","align":"full","layout":{"type":"constrained"}} -->
	<main class="wp-block-group alignfull">
		<?php bbpress_fse_template_include(); ?>
	</main>
	<!-- /wp:group -->

	<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->
	<?php
	$_wp_current_template_content = ob_get_clean(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

	bbp_set_template_included( $template );

	return $template;
}
add_filter( 'bbp_template_include_theme_supports', 'a8csp_bbpress_fse_support_theme_support' );


/**
 * Include the correct template part based on the current bbPress view
 *
 * @return void
 */
function a8csp_bbpress_fse_support_template_include(): void {
	if ( ! is_bbpress() ) {
		return;
	}

	//phpcs:disable Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace

	// Editing a user or Viewing a user
	if ( bbp_is_single_user_edit() || bbp_is_single_user() ) {
		bbp_get_template_part( 'content', 'single-user' );
	}

	// User favorites
	elseif ( bbp_is_favorites() ) {
		bbp_get_template_part( 'user', 'favorites' );
	}

	// User favorites
	elseif ( bbp_is_subscriptions() ) {
		bbp_get_template_part( 'user', 'subscriptions' );
	}

	// Single View
	elseif ( bbp_is_single_view() ) {
		bbp_get_template_part( 'content', 'single-view' );
	}

	// Search
	elseif ( bbp_is_search() ) {
		bbp_get_template_part( 'content', 'search' );
	}

	// Forum edit
	elseif ( bbp_is_forum_edit() ) {
		bbp_get_template_part( 'form', 'forum' );
	}

	// Single Forum
	elseif ( bbp_is_single_forum() ) {
		// Check forum caps
		if ( bbp_user_can_view_forum() ) {
			bbp_get_template_part( 'content', 'single-forum' );

			// Forum is private and user does not have caps
		} elseif ( bbp_is_forum_private() ) {
			bbp_get_template_part( 'feedback', 'no-access' );
		}
	}

	// Forum Archive
	elseif ( bbp_is_forum_archive() ) {
		bbp_get_template_part( 'content', 'archive-forum' );
	}

	// Topic merge
	elseif ( bbp_is_topic_merge() ) {
		bbp_get_template_part( 'form', 'topic-merge' );
	}

	// Topic split
	elseif ( bbp_is_topic_split() ) {
		bbp_get_template_part( 'form', 'topic-split' );
	}

	// Topic edit
	elseif ( bbp_is_topic_edit() ) {
		// If the forum id is set, check forum caps else display normal topic form
		if ( bbp_user_can_view_forum() ) {
			bbp_get_template_part( 'form', 'topic' );

			// Forum is private and user does not have caps
		} elseif ( bbp_is_forum_private( 0, false ) ) {
			bbp_get_template_part( 'feedback', 'no-access' );
		}
	}

	// Single Topic
	elseif ( bbp_is_single_topic() ) {
		// Check forum caps
		if ( bbp_user_can_view_forum() ) {
			bbp_get_template_part( 'content', 'single-topic' );

			// Forum is private and user does not have caps
		} elseif ( bbp_is_forum_private( 0, false ) ) {
			bbp_get_template_part( 'feedback', 'no-access' );
		}
	}

	// Topic Archive
	elseif ( bbp_is_topic_archive() ) {
		bbp_get_template_part( 'content', 'archive-topic' );
	}
	// Reply move
	elseif ( bbp_is_reply_move() ) {
		bbp_get_template_part( 'form', 'reply-move' );
	}

	// Editing a reply
	elseif ( bbp_is_reply_edit() ) {
		bbp_get_template_part( 'form', 'reply' );

		// Lock the reply from other edits
		bbp_set_post_lock( bbp_get_reply_id() );
	}

	// Single Reply
	elseif ( bbp_is_single_reply() ) {
		// Check forum caps
		if ( bbp_user_can_view_forum() ) {
			bbp_get_template_part( 'content', 'single-reply' );

			// Forum is private and user does not have caps
		} elseif ( bbp_is_forum_private( 0, false ) ) {
			bbp_get_template_part( 'feedback', 'no-access' );
		}
	}

	// Editing a topic tag
	elseif ( bbp_is_topic_tag_edit() ) {
		bbp_get_template_part( 'content', 'topic-tag-edit' );
	}

	// Viewing a topic tag
	elseif ( bbp_is_topic_tag() ) {
		bbp_get_template_part( 'content', 'archive-topic' );
	}
	//phpcs:enable Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace
}