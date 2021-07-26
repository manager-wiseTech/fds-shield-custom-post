<?php
/**
 * Trigger this file on Plugin uninstall
 *
 * @package  Shield
 */
// validate the unautorized or illegal access
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
	
}
/** comparing the delete settings options 
* This settings option is specified in the settings sub-menu named shield settings.
* The shield setting sub-menu page is only related to this plugin.
* if 'true' then after deletion of plugin all the data related to this plugin will be deleted.
* If not true then plugin will be deleted but the data related to the this plugin will be save in database and appears as the developer install the plugin again.

*/
if (get_option('delete_option')=='true') 
{
	// Clear Database stored data
	// here -1 means All posts
		$shields = get_posts( array( 'post_type' => 'shield', 'numberposts' => -1 ) );
			foreach( $shields as $shield ) {
					wp_delete_post( $shield->ID, true );
					}
		//delete all tags related to this post type
		$tags = get_tags( array('number' => 0,'hide_empty' => false));
	foreach ( $tags as $tag ) {		
		$tag_id = $tag->term_id;
		$args = array( 'post_type' => 'shield', 'posts_per_page'   => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'post_tag',
					'field' => 'id',
					'terms' => $tag_id
				)
			)
		);
		$postslist = get_posts( $args );
		$count_posts_with_this_tag = count($postslist);
		if ($count_posts_with_this_tag < 1 ){
			wp_delete_term( $tag_id, 'post_tag' );
		}
	}
	
	// delte all categories realted to this post
	$terms = get_terms( array('taxonomy' => 'category','hide_empty' => false
) );
	foreach($terms as $cat)
	{
	   wp_delete_category( $cat->term_id );
	}
		// // Access the database via SQL

		// creating global variable to perform database operations, $wpdb is very powerful variable of class WPDB and give access directly to database for operation but it is also very dangerous to use due to its scope and extensive power. 

		global $wpdb;

		//Deleting data from wp_posts table
		$wpdb->query( "DELETE FROM wp_posts WHERE post_type = 'shield'" );
		//Deleting data from wp_postmeta table
		$wpdb->query( "DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT ID FROM wp_posts)" );
		//Deleting data from wp_term_relationships table
		$wpdb->query( "DELETE FROM wp_term_relationships WHERE object_id NOT IN (SELECT id FROM wp_posts)" );
}