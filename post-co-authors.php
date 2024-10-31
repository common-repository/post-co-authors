<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://acquaintsoft.com/
 * @since             1.0.0
 * @package           Post_Co_Authors
 *
 * @wordpress-plugin
 * Plugin Name:       Post Co-Authors
 * Plugin URI:        http://acquaintsoft.com/
 * Description:       This plugin is Allows multiple authors to be assigned to a post.
 * Version:           1.0.0
 * Author:            Acquaintsoft
 * Author URI:        http://acquaintsoft.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       post-co-authors
 * Domain Path:       /languages
 */

// If this file is called directly, abort.

define( 'COAUTHORS_POST_VERSION', '3.2.1' );
/*---------------Main Plugin Class--------------*/
class CoAuthors_Post {

/*------------------Function Construct------------------------*/
	function __construct() {
		add_action( 'save_post', array( $this, 'pcauthor_coauthors_update_post' ), 10, 2 );
		add_action( 'add_meta_boxes', array( $this, 'pcauthor_add_coauthors_box' ) );
		add_action( 'add_meta_boxes', array( $this, 'pcauthor_remove_authors_box' ) );
		add_action( 'admin_menu', array( $this, 'pcauthor_my_plugin_menu' ));
		add_shortcode('co-authors', array( $this, 'pcauthor_get_co_author'));

		/*--------------- Activation & Deactivation Hooks ------------------------*/
		register_activation_hook( __FILE__, 'pcauthor_activate_post_co_authors' );
		register_deactivation_hook( __FILE__, 'pcauthor_deactivate_post_co_authors' );

		/**
		 * The core plugin class that is used to define internationalization,
		 * admin-specific hooks, and public-facing site hooks.
		 */
		require plugin_dir_path( __FILE__ ) . 'includes/class-post-co-authors.php';

	}

/*-----------------------Function to remove metabox from backend---------------------*/
	public function pcauthor_remove_authors_box() {
		remove_meta_box( 'authordiv', get_post_type(), 'normal' );
	}

	/*------------------ Adds a custom Authors box ------------------*/
	public function pcauthor_add_coauthors_box() {
		$option = get_option ('show_screen_metabox');
		if(empty($option)){
			$option = 'null';
		}
		add_meta_box( 'coauthorsdiv', 'Authors', array( $this, 'pcauthor_coauthors_meta_box' ), $option, 'normal', 'low' );
	}


/*------------------ Get all types of Users in metabox to select---------------------*/
	public function pcauthor_coauthors_meta_box( $post ) {
		global $post, $coauthors_post;

		$post_id = $post->ID;
		
		/*------------- Administrator ----------------*/

		$meta = get_post_meta($post_id, 'administrator_id');
		$auth_meta = explode(',', $meta[0]);
		$administrator = array(
			'role'         => 'administrator',
		 ); 
		$authors = get_users( $administrator );
		$administrator_i = 1;
		if(!empty($authors)){
			?>
			<b>Administrator</b><br>
			<?php
		}
		foreach ($authors as $author) {
			$auth_data = $author->data;
			if(in_array($auth_data->ID, $auth_meta)){
				$checked = 'checked';
			} else {
				$checked = '';
			}
			echo '<label for="administrator'.$administrator_i.'"><input type="checkbox" id="administrator'.$administrator_i.'" name="administrator'.$administrator_i.'" '.$checked.' value="'.$auth_data->ID.'"> '. $auth_data->user_login .'</label><br>';
			$administrator_i++;
		}

		/*------------- Author ----------------*/

		$meta = get_post_meta($post_id, 'author_id');
		$auth_meta = explode(',', $meta[0]);
		$author = array(
			'role'         => 'author',
		 ); 
		$authors = get_users( $author );
		$author_i = 1;
		if(!empty($authors)){
			?>
			<br><b>Author</b><br>
			<?php
		}
		foreach ($authors as $author) {
			$auth_data = $author->data;
			if(in_array($auth_data->ID, $auth_meta)){
				$checked = 'checked';
			} else {
				$checked = '';
			}
			echo '<label for="author'.$author_i.'"><input type="checkbox" id="author'.$author_i.'" name="author'.$author_i.'" '.$checked.' value="'.$auth_data->ID.'"> '. $auth_data->user_login .'</label><br>';
			$author_i++;
		}

		/*------------- Editor ----------------*/

		$meta = get_post_meta($post_id, 'editor_id');
		$auth_meta = explode(',', $meta[0]);
		$editor = array(
			'role'         => 'editor',
		 ); 
		$authors = get_users( $editor );
		$editor_i = 1;
		if(!empty($authors)){
			?>
			<br><b>Editor</b><br>
			<?php
		}
		foreach ($authors as $author) {
			$auth_data = $author->data;
			if(in_array($auth_data->ID, $auth_meta)){
				$checked = 'checked';
			} else {
				$checked = '';
			}
			echo '<label for="editor'.$editor_i.'"><input type="checkbox" id="editor'.$editor_i.'" name="editor'.$editor_i.'" '.$checked.' value="'.$auth_data->ID.'"> '. $auth_data->user_login .'</label><br>';
			$editor_i++;
		}
	}


/*------------- Save Authors assign to the post/page ----------------*/
	function pcauthor_coauthors_update_post( $post_id, $post ) {
		$authors = count(get_users());
		$administrator = array();
		$author = array();
		$editor = array();
		for ($i=1; $i <= $authors; $i++) { 
			if(isset($_POST['administrator'.$i]) && $_POST['administrator'.$i] != ''){
				$sel_admin=(int)$_POST['administrator'.$i];
				$administrator[] = $sel_admin;


			}
			if(isset($_POST['author'.$i]) && $_POST['author'.$i] != ''){
				$sel_author=(int)$_POST['author'.$i];
				$author[] = $sel_author;
			}
			
			if(isset($_POST['editor'.$i]) && $_POST['editor'.$i] != ''){
				$sel_editor=(int)$_POST['editor'.$i];
				$editor[] = $sel_editor;
			}
		}

		/*------------- Administrator ----------------*/
		$administrator = esc_html(trim(implode(',', $administrator)));
		$auth_meta = get_post_meta($post_id, 'administrator_id');
		if($auth_meta!= ''){
			update_post_meta($post_id, 'administrator_id',  $administrator);
		} else {
			add_post_meta($post_id, 'administrator_id', $administrator);
		}

		/*------------- Author ----------------*/
		$author = esc_html(trim(implode(',', $author)));
		$auth_meta = get_post_meta($post_id, 'author_id');
		if($auth_meta!= ''){
			update_post_meta($post_id, 'author_id',  $author);
		} else {
			add_post_meta($post_id, 'author_id', $author);
		}

		/*------------- Editor ----------------*/
		$editor = esc_html(trim(implode(',', $editor)));
		$auth_meta = get_post_meta($post_id, 'editor_id');
		if($auth_meta!= ''){
			update_post_meta($post_id, 'editor_id',  $editor);
		} else {
			add_post_meta($post_id, 'editor_id', $editor);
		}
	}

/*------------- Shortcode to show all selected authors in Frontend ----------------*/
//if (!function_exists('pcauthor_get_co_author')) { 
	function pcauthor_get_co_author($atts){
		if($atts == ''){
			$id = get_the_ID();
		} else{
			$id = $atts['id'];
		}
		$admin_meta = get_post_meta($id, 'administrator_id');
		$admin = explode(',', $admin_meta[0]);
		$auth_meta = get_post_meta($id, 'author_id');
		$auth = explode(',', $auth_meta[0]);
		$editor_meta = get_post_meta($id, 'editor_id');
		$editor = explode(',', $editor_meta[0]);

		$authors = array_merge($admin, $auth, $editor);
		$author_name = array();
		$author_link = array();
		foreach ($authors as $author_id) {
			if($author_id != ''){ 
				$author_name[] = get_author_name ($author_id);
				$author_link[] = get_the_author_meta ('user_url', $author_id);
			}
		}
		asort($author_name);
		foreach ($author_name as $key => $value) {
			$author .= 'Author Name: <a target="_blank" href="'.esc_url($author_link[$key]).'">'.sanitize_title(esc_html($value)).'</a><br>';
		}
		return $author;
	}
//}

/*------------- Add plugin author setting option in setting tab ----------------*/
function pcauthor_my_plugin_menu() {
	add_options_page( 'Co Author Options', 'Co Authors', 'manage_options', 'co_author_options', array( $this, 'pcauthor_co_author_options' ));
}

/*------------- Show co-authors setting page for co-authors plugin ----------------*/
function pcauthor_co_author_options() {
	?>
	<div class="wrap">
		<h1>Co Author Option</h1>
		<form method="post" action="">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="blogname">Show the Metabox:</label></th>
						<?php
							$args_types=array(
								'public'	=> true, // publicaly visible
							);
							$post_types = get_post_types($args_types);
						?><td><?php
						$s = 0;
						$screen = array();
						foreach ($post_types as $post_type) {
							$obj = get_post_type_object( $post_type );
							$option = get_option ('show_screen_metabox');
							if(isset($_POST['screen-'.$s]) && $_POST['screen-'.$s] != ''){
								$screen[] = (int)$_POST['screen-'.$s];
							}						
						
							if(!empty($option)){
								if(in_array($post_type, $screen)){
									$checked = 'checked';
								} else if(in_array($post_type, $option) && empty($_POST)){
									$checked = 'checked';
								} else {
									$checked = '';
								}
							} else if(!empty($screen)){
								if(in_array($post_type, $screen)){
									$checked = 'checked';
								} else {
									$checked = '';
								}
							}
							if($post_type != 'attachment' && $post_type != 'revision' && $post_type != 'nav_menu_item'){
								echo '<input type="checkbox" '.$checked.' name="screen-'.$s.'" id="screen" value="'.$post_type.'" >'.$obj->labels->singular_name.'<br>';
							}
							$s++;
						}
						?></td>
					</tr>
				</tbody>	
			</table>
			<p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit"></p>
		</form>
	</div>
	<?php
	if(isset($_POST) && !empty($_POST)){
		$post_types = get_post_types();
		$s = 0;
		$screen = array();
		foreach ($post_types as $post_type) {
			if(isset($_POST['screen-'.$s]) && $_POST['screen-'.$s] != ''){				
				$screen[] = (int)$_POST['screen-'.$s];
			}
			$s++;
		}
		$option = get_option ('show_screen_metabox');
		if($option != ''){
			update_option('show_screen_metabox', $screen);
		} else {
			add_option('show_screen_metabox', $screen);
		}
	}
}

/*------------------------------------------------------------*/

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-post-co-authors-activator.php
 */
function pcauthor_activate_post_co_authors() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-post-co-authors-activator.php';
	Post_Co_Authors_Activator::pcauthor_activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-post-co-authors-deactivator.php
 */
function pcauthor_deactivate_post_co_authors() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-post-co-authors-deactivator.php';
	Post_Co_Authors_Deactivator::pcauthor_deactivate();
}

}
global $coauthors_post;
$coauthors_post = new CoAuthors_Post();
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function pcauthor_run_post_co_authors() {

	$plugin = new Post_Co_Authors();
	$plugin->pcauthor_run();

}
pcauthor_run_post_co_authors();