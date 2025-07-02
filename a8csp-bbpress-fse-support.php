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
 * Plugin Name:             bbPress FSE Support
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

// If bbPress is not installed, exit early to prevent errors.
if ( ! class_exists( 'bbPress' ) ) {
	return;
}

/**
 * Add bbPress theme support for Full Site Editing (FSE).
 *
 * This filter replaces the default bbPress template output with a block-based structure
 * using template parts for the header and footer, and a main content area for bbPress content.
 *
 * @param string $template The path to the template file to include.
 *
 * @return string The (possibly modified) template path to include.
 */
function a8csp_bbpress_fse_support_theme_support( string $template ): string {
	// If BuddyPress is active or this is not a bbPress page, do not modify the template.
	if ( is_buddypress() || ! is_bbpress() ) {
		return $template;
	}

	// Use output buffering to capture the block-based template structure.
	global $_wp_current_template_content;
	ob_start();
	?>
	<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->

	<!-- wp:group {"tagName":"main","align":"full","layout":{"type":"constrained"}} -->
	<main class="wp-block-group alignfull">
		<?php bbpress_fse_template_include(); // Output the appropriate bbPress template part. ?>
	</main>
	<!-- /wp:group -->

	<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->
	<?php
	// Store the generated template content in the global variable for FSE compatibility.
	$_wp_current_template_content = ob_get_clean(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

	// Mark the template as included for bbPress.
	bbp_set_template_included( $template );

	return $template;
}
add_filter( 'bbp_template_include_theme_supports', 'a8csp_bbpress_fse_support_theme_support' );


/**
 * Output the correct bbPress template part based on the current view.
 *
 * This function checks the current bbPress context (user, forum, topic, reply, etc.)
 * and includes the appropriate template part using bbPress's template loader.
 *
 * @return void
 */
function a8csp_bbpress_fse_support_template_include(): void {
	// Only proceed if this is a bbPress page.
	if ( ! is_bbpress() ) {
		return;
	}

	//phpcs:disable Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace

	// Editing a user or Viewing a user profile page.
	if ( bbp_is_single_user_edit() || bbp_is_single_user() ) {
		bbp_get_template_part( 'content', 'single-user' );
	}

	// User favorites page.
	elseif ( bbp_is_favorites() ) {
		bbp_get_template_part( 'user', 'favorites' );
	}

	// User subscriptions page.
	elseif ( bbp_is_subscriptions() ) {
		bbp_get_template_part( 'user', 'subscriptions' );
	}

	// Single view page (custom bbPress views).
	elseif ( bbp_is_single_view() ) {
		bbp_get_template_part( 'content', 'single-view' );
	}

	// Search results page.
	elseif ( bbp_is_search() ) {
		bbp_get_template_part( 'content', 'search' );
	}

	// Forum edit form.
	elseif ( bbp_is_forum_edit() ) {
		bbp_get_template_part( 'form', 'forum' );
	}

	// Single forum page.
	elseif ( bbp_is_single_forum() ) {
		// Check if the current user can view the forum.
		if ( bbp_user_can_view_forum() ) {
			bbp_get_template_part( 'content', 'single-forum' );

			// Forum is private and user does not have permission.
		} elseif ( bbp_is_forum_private() ) {
			bbp_get_template_part( 'feedback', 'no-access' );
		}
	}

	// Forum archive (list of all forums).
	elseif ( bbp_is_forum_archive() ) {
		bbp_get_template_part( 'content', 'archive-forum' );
	}

	// Topic merge form.
	elseif ( bbp_is_topic_merge() ) {
		bbp_get_template_part( 'form', 'topic-merge' );
	}

	// Topic split form.
	elseif ( bbp_is_topic_split() ) {
		bbp_get_template_part( 'form', 'topic-split' );
	}

	// Topic edit form.
	elseif ( bbp_is_topic_edit() ) {
		// If the forum id is set, check forum caps else display normal topic form.
		if ( bbp_user_can_view_forum() ) {
			bbp_get_template_part( 'form', 'topic' );

			// Forum is private and user does not have permission.
		} elseif ( bbp_is_forum_private( 0, false ) ) {
			bbp_get_template_part( 'feedback', 'no-access' );
		}
	}

	// Single topic page.
	elseif ( bbp_is_single_topic() ) {
		// Check if the current user can view the forum.
		if ( bbp_user_can_view_forum() ) {
			bbp_get_template_part( 'content', 'single-topic' );

			// Forum is private and user does not have permission.
		} elseif ( bbp_is_forum_private( 0, false ) ) {
			bbp_get_template_part( 'feedback', 'no-access' );
		}
	}

	// Topic archive (list of all topics).
	elseif ( bbp_is_topic_archive() ) {
		bbp_get_template_part( 'content', 'archive-topic' );
	}
	// Reply move form.
	elseif ( bbp_is_reply_move() ) {
		bbp_get_template_part( 'form', 'reply-move' );
	}

	// Editing a reply form.
	elseif ( bbp_is_reply_edit() ) {
		bbp_get_template_part( 'form', 'reply' );

		// Lock the reply from other edits.
		bbp_set_post_lock( bbp_get_reply_id() );
	}

	// Single reply page.
	elseif ( bbp_is_single_reply() ) {
		// Check if the current user can view the forum.
		if ( bbp_user_can_view_forum() ) {
			bbp_get_template_part( 'content', 'single-reply' );

			// Forum is private and user does not have permission.
		} elseif ( bbp_is_forum_private( 0, false ) ) {
			bbp_get_template_part( 'feedback', 'no-access' );
		}
	}

	// Editing a topic tag form.
	elseif ( bbp_is_topic_tag_edit() ) {
		bbp_get_template_part( 'content', 'topic-tag-edit' );
	}

	// Viewing a topic tag archive.
	elseif ( bbp_is_topic_tag() ) {
		bbp_get_template_part( 'content', 'archive-topic' );
	}
	//phpcs:enable Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace
}