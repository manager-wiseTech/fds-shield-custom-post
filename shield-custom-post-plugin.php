<?php
/**
 * @package  Shield
 */
/*
Plugin Name: Shield-custom-post Plugin
Plugin URI: 
Description: This is shields custom post type plugin.
Version: 1.0.0
Author: Muhammad Tariq Khan
Author URI:
License: GPLv2 or later
Text Domain: 
*/
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
Copyright 2005-2015 Automattic, Inc.
*/

require 'plugin-update-checker-master/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/manager-wiseTech/fds-shield-custom-post/',
	__FILE__,
	'fds-shield-custom-post'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication('your-token-here');







defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );
// defining a class
class Shield
{
	//class variable
	public $plugin;
	


	//class constructor method or function. Constructor is the method in the class which is called when an object of that class is instantiated.
	function __construct() {
		$this->plugin = plugin_basename( __FILE__ );

	}
	// This function is called with the refrence of the class object.
	// This function contains all the actions hooks for the init,style and script enqueue function and admin sub menu function.  
	function register() {
		
		// This action hook create a custom post type on the intitialization.
			add_action( 'init', array( $this, 'create_shield_cpt' ) );
			
			add_action("admin_init", array($this,"upload_csv"));
			add_action( 'admin_menu', array( $this, 'shield_import_csv' ) );
			
			
		// This action hook enqueue the scripts and style by calling the enqueue function.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		// This action hook add sub-menu to the settings menu by calling the add-admin_pages function in the hook.	
			add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );

			// add_filter( "plugin_action_links_$this->plugin", array( $this, 'settings_link' ) );	
			add_action( 'add_meta_boxes',array( $this, 'meta_box_add' )  );

			
			add_action('save_post', array( $this, 'wp_save_metabox_data' ), 10, 2);
		
		}
		
		function meta_box_add()
		{
			add_meta_box( 'leftpins', 'Left Pins', array( $this, 'leftpins'),'shield');
		    add_meta_box( 'rightpins', 'Right Pins', array( $this, 'rightpins'),'shield');
		   	add_meta_box( 'shieldurl', 'Shield URL', array( $this, 'shield_url'),'shield');
		    add_meta_box( 'tags', 'Tags', array( $this, 'tags'),'shield');
		    //add_meta_box( 'customimg', 'Custom Images', array( $this, 'custom_images'),'shield');
		     
		    add_meta_box( 'makerurl', 'Maker URL', array( $this, 'maker_url'),'shield');
		    add_meta_box( 'opensrc', 'Open Source', array( $this, 'open_src'),'shield');
		    add_meta_box( 'license', 'License', array( $this, 'license'),'shield');
		    add_meta_box( 'source', 'Source', array( $this, 'source'),'shield');
		    add_meta_box('version','Shield Version',array($this,'shield_version'),'shield');
		  	add_meta_box('price','Shield Price',array($this,'shield_price'),'shield');
		  	add_meta_box('voltage','Shield Voltage',array($this,'shield_voltage'),'shield');
		  	add_meta_box('current_draw','Shield Current Draw',array($this,'shield_current_draw'),'shield');
		  	add_meta_box('datasheets','Shield Datasheets',array($this,'shield_datasheets'),'shield');
		    add_meta_box('schematics','Shield Schematics',array($this,'shield_schematics'),'shield');
		    add_meta_box('mishadiv','Add first custom image',	array($this,'misha_print_box'),	'shield');
		    add_meta_box('mishadiv2','Add Second custom image',	array($this,'misha_print_box_2'),'shield');
		    add_meta_box( 'note', 'Note', array( $this, 'note'),'shield');
		    
		  	
		}

		function misha_image_uploader_field( $name, $value = '') {
		$image = ' button">Upload First image';
		$image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
		$display = 'none'; // display state to the "Remove image" button
	 
		if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {
	 
			// $image_attributes[0] - image URL
			// $image_attributes[1] - image width
			// $image_attributes[2] - image height
	 
			$image = '"><img src="' . $image_attributes[0] . '" style="max-width:30%;display:block;" />';
			$display = 'inline-block';
	 
		} 
	 
		return '
		<div>
			<a href="#" class="misha_upload_image_button' . $image . '</a>
			<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
			<a href="#" class="misha_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
		</div>';
	}
		function misha_print_box() {
			wp_nonce_field(basename(__FILE__),'wp_cpt_img_nonce');	
			$id = get_the_ID();
		$meta_key = 'custom_image1';
		echo $this->misha_image_uploader_field( $meta_key, get_post_meta($id, $meta_key, true) );
		}


		function misha_image_uploader_field_2( $name, $value = '') {
		$image = ' button">Upload second image';
		$image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
		$display = 'none'; // display state to the "Remove image" button
	 
		if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {
	 
			// $image_attributes[0] - image URL
			// $image_attributes[1] - image width
			// $image_attributes[2] - image height
	 
			$image = '"><img src="' . $image_attributes[0] . '" style="max-width:30%;display:block;" />';
			$display = 'inline-block';
	 
		} 
	 
		return '
		<div>
			<a href="#" class="misha_upload_image_button_2' . $image . '</a>
			<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
			<a href="#" class="misha_remove_image_button_2" style="display:inline-block;display:' . $display . '">Remove image</a>
		</div>';
	}
	



		function misha_print_box_2() {
			wp_nonce_field(basename(__FILE__),'wp_cpt_img_2_nonce');	
			$id = get_the_ID();
		$meta_key = 'custom_image2';
		echo $this->misha_image_uploader_field_2( $meta_key, get_post_meta($id, $meta_key, true) );
		}
		
		 function rightpins()
		{
			wp_nonce_field(basename(__FILE__),'wp_cpt_rightpins_nonce');	
		    ?><p class="description">Check the checkboxes of the left ON Pins of the shield</p>
		<ul class="acf-checkbox-list acf-bl">
			<?php 
				$id = get_the_ID();
				$left_pins = get_post_meta($id,'left_pins',true);
				if (!is_array($left_pins)) {
	
						$left_pins = [];
					}
					
				  ?>
			<li><label><input type="checkbox" name="left[]" value="RESET"
			 <?php if (in_array("RESET", $left_pins)){echo "checked"; }?>
  			/>RESET</label></li>
			<li><label><input type="checkbox" name="left[]" value="3.3V"
				<?php if (in_array("3.3V", $left_pins)){echo "checked"; }?>
			/>3.3V</label></li>
			<li><label><input type="checkbox" name="left[]" value="5V"
				<?php if (in_array("5V", $left_pins)){echo "checked"; }?>
			/>5V</label></li>
			<li><label><input type="checkbox" name="left[]" value="GND1"
				<?php if (in_array("GND1", $left_pins)){echo "checked"; }?>
			/>GND1</label></li>
			<li><label><input type="checkbox" name="left[]" value="GND2"
				<?php if (in_array("GND2", $left_pins)){echo "checked"; }?>
			/>GND2</label></li>
			<li><label><input type="checkbox" name="left[]" value="VIN"
				<?php if (in_array("VIN", $left_pins)){echo "checked"; }?>
			/>VIN</label></li>
			<li><label><input type="checkbox" name="left[]" value="A0"
				<?php if (in_array("A0", $left_pins)){echo "checked"; }?>
			/>A0</label></li>
			<li><label><input type="checkbox" name="left[]" value="A1"
				<?php if (in_array("A1", $left_pins)){echo "checked"; }?>
			/>A1</label></li>
			<li><label><input type="checkbox" name="left[]" value="A2"
				<?php if (in_array("A2", $left_pins)){echo "checked"; }?>
			/>A2</label></li>
			<li><label><input type="checkbox" name="left[]" value="A3"
				<?php if (in_array("A3", $left_pins)){echo "checked"; }?>
			/>A3</label></li>
			<li><label><input type="checkbox" name="left[]" value="A4"
				<?php if (in_array("A4", $left_pins)){echo "checked"; }?>
			/>A4</label></li>
			<li><label><input type="checkbox" name="left[]" value="A5"
				<?php if (in_array("A5", $left_pins)){echo "checked"; }?>
			/>A5</label></li>
			</ul>
			<?php
					}
			function leftpins()
		{
		    wp_nonce_field(basename(__FILE__),'wp_cpt_leftpins_nonce');	
		    ?><p class="description">check the checkboxes of the LEFT ON Pins of the shield</p>
		<ul class="acf-checkbox-list acf-bl">
			<?php 
				$id = get_the_ID();
				$right_pins = get_post_meta($id,'right_pins',true); 
					if (!is_array($right_pins)) {
						$right_pins = [];

					}
				 ?>
			<li><label><input type="checkbox" name="right[]" value="AREF"
				<?php if (in_array("AREF", $right_pins)){echo "checked"; }?>
			/>AREF</label></li>
			<li><label><input type="checkbox" name="right[]" value="GND"
				<?php if (in_array("GND", $right_pins)){echo "checked"; } ?>
			/>GND</label></li>
			<li><label><input type="checkbox" name="right[]" value="D13/SCK"
				<?php if (in_array("D13/SCK", $right_pins)){echo "checked"; } ?>
			/>D13/SCK</label></li>
			<li><label><input type="checkbox" name="right[]" value="D12/MISO"
				<?php if (in_array("D12/MISO", $right_pins)){echo "checked"; } ?>
			/>D12/MISO</label></li>
			<li><label><input type="checkbox" name="right[]" value="D11/MOSI~"
				<?php if (in_array("D11/MOSI~", $right_pins)){echo "checked"; } ?>
			/>D11/MOSI~</label></li>
			<li><label><input type="checkbox" name="right[]" value="D10/SS~"
				<?php if (in_array("D10/SS~", $right_pins)){echo "checked"; } ?>
			/>D10/SS~</label></li>
			<li><label><input type="checkbox" name="right[]" value="D9~"
				<?php if (in_array("D9~", $right_pins)){echo "checked"; } ?>
			/>D9~</label></li>
			<li><label><input type="checkbox" name="right[]" value="D8"
				<?php if (in_array("D8", $right_pins)){echo "checked"; } ?>
			/>D8</label></li>
			<li><label><input type="checkbox" name="right[]" value="D7"
				<?php if (in_array("D7", $right_pins)){echo "checked"; } ?>
			/>D7</label></li>
			<li><label><input type="checkbox" name="right[]" value="D6"
				<?php if (in_array("D6", $right_pins)){echo "checked"; } ?>
			/>D6</label></li>
			<li><label><input type="checkbox" name="right[]" value="D5"
				<?php if (in_array("D5", $right_pins)){echo "checked"; } ?>
			/>D5</label></li>
			<li><label><input type="checkbox" name="right[]" value="D4"
				<?php if (in_array("D4", $right_pins)){echo "checked"; } ?>
			/>D4</label></li>
			<li><label><input type="checkbox" name="right[]" value="D3"
				<?php if (in_array("D3", $right_pins)){echo "checked"; } ?>
			/>D3</label></li>
			<li><label><input type="checkbox" name="right[]" value="D2"
				<?php if (in_array("D2", $right_pins)){echo "checked"; } ?>
			/>D2</label></li>
			<li><label><input type="checkbox" name="right[]" value="D1/TX"
				<?php if (in_array("D1/TX", $right_pins)){echo "checked"; } ?>
			/>D1/TX</label></li>
			<li><label><input type="checkbox" name="right[]" value="D0/RX"
				<?php if (in_array("D0/RX", $right_pins)){echo "checked"; } ?>
			/>D0/RX</label></li>
			</ul>
			<?php
					}
		

		function shield_url()
		{
			$id = get_the_ID();
			wp_nonce_field(basename(__FILE__),'wp_cpt_shield_url_nonce');	

		    ?><table>
			<tr>
			<td><label>Enter URL</label></td>
			  <?php
			 
		        $s_url = get_post_meta($id, 'shield_url',true);
		    	if (!is_array($s_url)) {
						$s_url = [];

					}
		        ?>
			<td><input name="s_url" value="<?php echo $s_url['url']; ?>" type="url" /></td>
			</tr>
			<tr>
			<td><label>Enter link title</label></td>
			<td><input name="s_url_title" value="<?php echo $s_url['title']; ?>" type="text" /></td>
			</tr>
			</table>
			<?php
			
		}
		function tags()
		{
			wp_nonce_field(basename(__FILE__),'wp_cpt_tags_nonce');	
		    ?>
		    <table>
			<tr>
			<td><label>Enter Tags</label></td>
			<?php 
			$id = get_the_ID();
			$post_tags = get_the_tags( $id );
			if (!is_array($post_tags)) {
						$post_tags = [];

				}
			foreach ($post_tags as $t) {
				$tags .= $t->name . ', '; 
			}
			?>
			<td><input style="width: 500px;" name="tags" value="<?php echo $tags; ?>" type="text"/></td>
			</tr>
			<tr>
				<td><span class="dashicons-before dashicons-arrow-right-alt"></span></td>
				<td>insert comma , to seprate each tag</td>
			</tr>
			
			</table>
			<?php
			
		}
		function maker_url()
		{
			wp_nonce_field(basename(__FILE__),'wp_cpt_maker_url_nonce');	
		    ?>
		    <table>
		    	<?php $id = get_the_ID(); ?>
		    	<?php $maker = get_post_meta($id, 'maker',true); 
		    	if (!is_array($maker)) {
						$maker = [];

				}
		    	?>
		    	
			<tr>
			<td><label>Enter URL</label></td>
			<td><input name="m_url" value="<?php echo $maker['url']; ?>" type="url" /></td>
			</tr>
			<tr>
			<td><label>Enter link title</label></td>
			<td><input name="m_url_title" value="<?php echo $maker['title']; ?>" type="text" /></td>
			</tr>
			</table>
			<?php
			
		}
		function open_src()
		{
			wp_nonce_field(basename(__FILE__),'wp_cpt_open_src_nonce');	
		    ?>
			<table>
				<tr>
					<?php $id = get_the_ID(); ?>
		    	<?php $open_source = get_post_meta($id, 'open_source',true);
		  		if (empty($open_source)) {
		  			$open_source="unknown";
		  		}
		   ?>
				
				<td><input type="radio" name="o_src" value="yes"<?php if($open_source=='yes'){echo "checked";} ?> />Yes</td>
				<td><input type="radio" name="o_src" value="no" <?php if($open_source=='no'){echo "checked";}?>/>No</td>
				<td><input type="radio" name="o_src" value="unknown"<?php if($open_source=='unknown'){echo "checked";} ?>/>Unknown</td></tr>
			</table>
			<?php
		}

		function license()
		{

			wp_nonce_field(basename(__FILE__),'wp_cpt_license_nonce');	
		    ?>
			<table>
				<?php $id = get_the_ID(); ?>
		    	<?php $license = get_post_meta($id, 'license',true);
		    	if (!is_array($license)) {
						$license = [];

				} 
		    	 ?>
			<tr>
			<td><label>Enter URL</label></td>
			<td><input name="l_url" value="<?php echo $license['url']; ?>" type="url" /></td>
			</tr>
			<tr>
			<td><label>Enter link title</label></td>
			<td><input name="l_url_title" value="<?php echo $license['title']; ?>" type="text" /></td>
			</tr>
			</table>
			<?php
			
		}	
		function source()
		{
			wp_nonce_field(basename(__FILE__),'wp_cpt_source_nonce');	
		    ?>
		    <table>
		    	<?php $id = get_the_ID(); ?>
		    	<?php $source = get_post_meta($id, 'source',true);
		    	if (!is_array($source)) {
						$source = [];

				}
		    	 ?>
			<tr>
			<td><label>Enter URL</label></td>
			<td><input name="src_url" value="<?php echo $source['url']; ?>" type="url" /></td>
			</tr>
			<tr>
			<td><label>Enter link title</label></td>
			<td><input name="src_url_title" value="<?php echo $source['title']; ?>" type="text" /></td>
			</tr>
			</table>
			<?php
			
		}
		function note()
		{
			wp_nonce_field(basename(__FILE__),'wp_cpt_note_nonce');	
		    ?>
		    <table>
		    	<?php $id = get_the_ID(); ?>
		    	<?php $note = get_post_meta($id, 'note',true); 
		    	if ($note == "") {
		    		$note = "";
		    	}
		    		?>
			<tr>
			<td><label>Note</label></td>
			<td><textarea  name="note"><?php echo $note; ?></textarea></td>
			</tr>
			</table>
			<?php 
			
		}
		function shield_version()
		{
			wp_nonce_field(basename(__FILE__),'wp_cpt_version_nonce');
				$id = get_the_ID(); 
		    	$version = get_post_meta($id, 'version',true); 
		    	if ($version == "") {
		    		$version = "";
		    	}
		    	
			?>
				<table>
					<tr>
						<td><label>Shield Version</label></td>
						<td><input type="text" name="version" value="<?php echo $version; ?>"></td>
					</tr>
				</table>
			<?php
		}
		function shield_price()
		{
			wp_nonce_field(basename(__FILE__),'wp_cpt_price_nonce');
			$id = get_the_ID(); 
		    	$price = get_post_meta($id, 'price',true); 
		    	if ($price == "") {
		    		$price = "";
		    	}
			?>
				<table>
					<tr>
						<td><label>Shield Price</label></td>
						<td><input type="text" name="price" value="<?php echo $price; ?>"></td>
					</tr>
				</table>
			<?php
		}
		function shield_voltage()
		{
			wp_nonce_field(basename(__FILE__),'wp_cpt_voltage_nonce');
			$id = get_the_ID(); 
		    	$voltage = get_post_meta($id, 'voltage',true); 
		    	if ($voltage == "") {
		    		$voltage = "";
		    	}
			?>
				<table>
					<tr>
						<td><label>Shield voltage</label></td>
						<td><input type="text" name="voltage" value="<?php echo $voltage; ?>"></td>
					</tr>
				</table>
			<?php
		}
		function shield_current_draw()
		{
			wp_nonce_field(basename(__FILE__),'wp_cpt_current_draw_nonce');
			$id = get_the_ID(); 
		    	$current_draw = get_post_meta($id, 'current_draw',true); 
		    	if ($current_draw == "") {
		    		$current_draw = "";
		    	}
			?>
				<table>
					<tr>
						<td><label>Shield current Draw</label></td>
						<td><input type="text" name="current_draw" value="<?php echo $current_draw; ?>"></td>
					</tr>
				</table>
			<?php
		}
		function shield_datasheets(){
		    wp_nonce_field(basename(__FILE__),'wp_cpt_datasheet_nonce');
		    $id = get_the_ID();
		    $resources = get_post_meta($id,'resources',true);
		    if(strpos($resources['title'], '#') !== false){
		        $title_arr = explode('#', $resources['title']);
                $url_arr = explode('#', $resources['url']);
               $resource_url = $url_arr[1];
		    }
		    else
		    {
		        if($resources['title'] == "Datasheet" || $resources['title'] == "datasheet"){
		            $resource_url = $resources['url'];    
		        }
		        
		    }
		    ?>
		    <table>
					<tr>
						<td><label>Shield Datasheets</label></td>
						<td><input type="url" name="datasheets" value="<?php echo $resource_url; ?>"></td>
					</tr>
				</table>
		    <?php
		}
		
		function shield_schematics(){
		    wp_nonce_field(basename(__FILE__),'wp_cpt_schematics_nonce');
		    $id = get_the_ID();
		    $resources = get_post_meta($id,'resources',true);
		     if(strpos($resources['title'], '#') !== false){
		        $title_arr = explode('#', $resources['title']);
                $url_arr = explode('#', $resources['url']);
               $resource_url = $url_arr[0];
		    }
		    else{
		        if($resources['title'] == "Schematics" || $resources['title'] == "schematics"){
		            $resource_url = $resources['url']; 
		        }
		    }
		     ?>
		    <table>
					<tr>
						<td><label>Shield Datasheets</label></td>
						<td><input type="url" name="schematics" value="<?php echo $resource_url; ?>"></td>
					</tr>
				</table>
		    <?php
		}
		
		
		function wp_save_metabox_data($post_id, $post) {

		    // verifying slug value
		    $post_slug = "shield";
		    if ($post_slug != $post->post_type) {
		        return;
		    }
		    //save value to db field
		    //updating shield URL and URL title
		    if (!isset($_POST['wp_cpt_shield_url_nonce']) || !wp_verify_nonce($_POST['wp_cpt_shield_url_nonce'], basename(__FILE__))) {
			        return $post_id;
			}

		    $shield_url = '';
		    if (isset($_POST['s_url'])) {
		        $shield_url = sanitize_text_field($_POST['s_url']);
		    } else {
		        $shield_url = '';
		    }
		    //schmatics and datasheet
		    if(isset($_POST['datasheets'])){
		        $ds = $_POST['datasheets'];
		    }
		    
		    if(isset($_POST['schematics'])){
		        $sch = $_POST['schematics'];
		    }
		    if(!empty($ds) && !empty($sch)){
                $resource_url = $ds.'#'.$sch;
                $resource_title = "Schematics"."#"."Datasheets";
            }
            else{
                if(!empty($ds))
                {
                    $resource_url = $ds;
                    $resource_title = "Datasheets";
                }
                if(!empty($sch)){
                    $resource_url = $sch;
                    $resource_title = "Schematics";
                }
            }
           
            $resources = array(
                'title' => $resource_title,
                'url' => $resource_url,
                'target' => '_blank'
                );
             update_post_meta( $post_id, 'resources', $resources );
		    //shield url title
		     $shield_url_title = '';
		    if (isset($_POST['s_url_title'])) {
		        $shield_url_title = sanitize_text_field($_POST['s_url_title']);
		    } else {
		        $shield_url_title = '';
		    }
		    $link = array(
		        'title'   => $shield_url_title,
		        'url'   => $shield_url,
		        'target'  => '_blank'
		      );
		    update_post_meta( $post_id, 'shield_url', $link );

		    //Updating Tags

		    if (!isset($_POST['wp_cpt_tags_nonce']) || !wp_verify_nonce($_POST['wp_cpt_tags_nonce'], basename(__FILE__))) {
			        return $post_id;
			}

		     if (isset($_POST['tags'])) {
		        $tags = $_POST['tags'];
		        $tag_list =  explode(",",$tags);
		    } else {
		        $tags = '';
		    }
		   wp_set_post_tags( $post_id,$tag_list);
		   update_post_meta($post_id,'tags',$tag_list);

		    //updating maker url and title 
		   if (!isset($_POST['wp_cpt_maker_url_nonce']) || !wp_verify_nonce($_POST['wp_cpt_maker_url_nonce'], basename(__FILE__))) {
			        return $post_id;
			}
		  
		    $maker = '';
		    if (isset($_POST['m_url'])) {
		        $maker_url = sanitize_text_field($_POST['m_url']);
		    } else {
		        $maker_url = '';
		    }
		    
		    //maker url title
		     $maker_url_title = '';
		    if (isset($_POST['m_url_title'])) {
		        $maker_url_title = sanitize_text_field($_POST['m_url_title']);
		    } else {
		        $maker_url_title = '';
		    }
		    $link = array(
		        'title'   => $maker_url_title,
		        'url'   => $maker_url,
		        'target'  => '_blank'
		      );
		    update_post_meta( $post_id, 'maker', $link );
		     //updating open source
		    if (!isset($_POST['wp_cpt_open_src_nonce']) || !wp_verify_nonce($_POST['wp_cpt_open_src_nonce'], basename(__FILE__))) {
			        return $post_id;
			}
		    
		    $o_src = '';
		    if (isset($_POST['o_src'])) {
		    	$o_src = sanitize_text_field($_POST['o_src']);
		    }
		    else
		    {
		    	$o_src = "unknown";
		    }
		    update_post_meta($post_id,'open_source',$o_src);

		    
		     //updating license url and title 
		    if (!isset($_POST['wp_cpt_license_nonce']) || !wp_verify_nonce($_POST['wp_cpt_license_nonce'], basename(__FILE__))) {
			        return $post_id;
			}
		    $license = '';
		    if (isset($_POST['l_url'])) {
		        $license_url = sanitize_text_field($_POST['l_url']);
		    } else {
		        $license_url = '';
		    }
		    
		    //license url title
		    
		     $license_url_title = '';
		    if (isset($_POST['l_url_title'])) {
		        $license_url_title = sanitize_text_field($_POST['l_url_title']);
		    } else {
		        $license_url_title = '';
		    }
		    $link = array(
		        'title'   => $license_url_title,
		        'url'   => $license_url,
		        'target'  => '_blank'
		      );
		    update_post_meta( $post_id, 'license', $link );


		    //updating source URL and URL title
		    if (!isset($_POST['wp_cpt_source_nonce']) || !wp_verify_nonce($_POST['wp_cpt_source_nonce'], basename(__FILE__))) {
			        return $post_id;
			}
		    $src_url = '';
		    if (isset($_POST['s_url'])) {
		        $src_url = sanitize_text_field($_POST['src_url']);
		    } else {
		        $src_url = '';
		    }
		    
		    //shield url title
		     $src_url_title = '';
		    if (isset($_POST['s_url_title'])) {
		        $src_url_title = sanitize_text_field($_POST['src_url_title']);
		    } else {
		        $src_url_title = '';
		    }
		    $link = array(
		        'title'   => $src_url_title,
		        'url'   => $src_url,
		        'target'  => '_blank'
		      );
		    update_post_meta( $post_id, 'source', $link );

		    //updating note
			if (!isset($_POST['wp_cpt_note_nonce']) || !wp_verify_nonce($_POST['wp_cpt_note_nonce'], basename(__FILE__))) {
			        return $post_id;
			} 
		    $note = '';
		    if (isset($_POST['note'])) {
		    	$note = sanitize_text_field($_POST['note']);
		    }
		    else
		    {
		    	$note = "";
		    }
		    update_post_meta($post_id,'note',$note);


		   

		    //updating left pins of shield
		     if (!isset($_POST['wp_cpt_leftpins_nonce']) || !wp_verify_nonce($_POST['wp_cpt_leftpins_nonce'], basename(__FILE__))) {
		        return $post_id;
		    } 

		    if (isset($_POST['left'])) {
		    	
			        foreach($_POST['left'] as $left_pins){
			           $l_pins = $l_pins . $left_pins.",";
			        }
			        $l_pins = rtrim($l_pins, ',');
			        
			        $l_pins = (explode(",",$l_pins));
		    }
		    update_post_meta( $post_id, 'left_pins', $l_pins );
		    if (!isset($_POST['wp_cpt_rightpins_nonce']) || !wp_verify_nonce($_POST['wp_cpt_rightpins_nonce'], basename(__FILE__))){
		    	return $post_id;
		    }
		    if (isset($_POST['right']))
			{
				foreach($_POST['right'] as $right_pins){
			          $r_pins = $r_pins . $right_pins.",";
			        }
			    $r_pins = rtrim($r_pins, ',');
			    $r_pins = explode(",",$r_pins);
			}

		    update_post_meta( $post_id, 'right_pins', $r_pins );
		    //version
		    if (!isset($_POST['wp_cpt_version_nonce']) || !wp_verify_nonce($_POST['wp_cpt_version_nonce'], basename(__FILE__))) {
			        return $post_id;
			}
			if (isset($_POST['version'])) {
				$version = sanitize_text_field($_POST['version']);
			}
			else
			{
				$version = "";
			}
			update_post_meta($post_id,'version',$version);
			//price
		    if (!isset($_POST['wp_cpt_price_nonce']) || !wp_verify_nonce($_POST['wp_cpt_price_nonce'], basename(__FILE__))) {
			        return $post_id;
			}
			if (isset($_POST['price'])) {
				$price = sanitize_text_field($_POST['price']);
			}
			else
			{
				$price = "";
			}
			update_post_meta($post_id,'price',$price);		    
			//voltage
		    if (!isset($_POST['wp_cpt_voltage_nonce']) || !wp_verify_nonce($_POST['wp_cpt_voltage_nonce'], basename(__FILE__))) {
			        return $post_id;
			}
			if (isset($_POST['voltage'])) {
				$voltage = sanitize_text_field($_POST['voltage']);
			}
			else
			{
				$voltage = "";
			}
			update_post_meta($post_id,'voltage',$voltage);
			//current draw
		    if (!isset($_POST['wp_cpt_current_draw_nonce']) || !wp_verify_nonce($_POST['wp_cpt_current_draw_nonce'], basename(__FILE__))) {
			        return $post_id;
			}
			if (isset($_POST['current_draw'])) {
				$current_draw = sanitize_text_field($_POST['current_draw']);
			}
			else
			{
				$current_draw = "";
			}
			update_post_meta($post_id,'current_draw',$current_draw);
			
			
		   
    
    
    //Uploading schematics 
    
    
    
    
    
			if (!isset($_POST['wp_cpt_img_2_nonce']) || !wp_verify_nonce($_POST['wp_cpt_img_2_nonce'], basename(__FILE__))) {
				return $post_id;
			}
			
			$meta_key = 'custom_image1';
			update_post_meta( $post_id, $meta_key, $_POST[$meta_key] );
			$m_key = 'custom_image2';
 			update_post_meta( $post_id, $m_key, $_POST[$m_key]);
 			return $post_id;
		
	   
          
	}
	
		public function add_admin_pages() {

			//This is the actual line which is adding shield settings submenu in the settings menu. It calls the admin_index function.

			//The admin_index function is including the file that contains the main template of the shield settings page named admin.php

			add_options_page('shield Settings', 'shield settings', 'manage_options', 'shield-settings', array( $this, 'admin_index' ) );
		}
		//The admin_index function is including the file that contains the main template of the shield settings page named admin.php
		public function admin_index() {

			require plugin_dir_path( __FILE__ ) . 'templates/admin.php';
		}
	function activate() {
		// generated a Custom Post Type
		$this->create_shield_cpt();
		// flush rewrite rules
		flush_rewrite_rules();
	}
	function deactivate() {
		// flush rewrite rules
		flush_rewrite_rules();
	}
	function enqueue() {
		// enqueue all our scripts
		wp_enqueue_style( 'mypluginstyle', plugins_url( '/assets/mystyle.css', __FILE__ ) );
		wp_enqueue_script( 'mypluginscript', plugins_url( '/assets/myscript.js', __FILE__ ) );
		wp_enqueue_script( 'mypluginscript2', plugins_url( '/assets/myscript2.js', __FILE__ ) );
	}
	//This function contains the code of creating custom post type. 
	function create_shield_cpt() {

	$labels = array(
		'name' => _x( 'Shields', 'Post Type General Name', 'shield' ),
		'singular_name' => _x( 'Shield', 'Post Type Singular Name', 'shield' ),
		'menu_name' => _x( 'Shields', 'Admin Menu text', 'shield' ),
		'name_admin_bar' => _x( 'Shield', 'Add New on Toolbar', 'shield' ),
		'archives' => __( 'Shield Archives', 'shield' ),
		'attributes' => __( 'Shield Attributes', 'shield' ),
		'parent_item_colon' => __( 'Parent Shield:', 'shield' ),
		'all_items' => __( 'All Shields', 'shield' ),
		'add_new_item' => __( 'Add New Shield', 'shield' ),
		'add_new' => __( 'Add New Shield', 'shield' ),
		'new_item' => __( 'New Shield', 'shield' ),
		'edit_item' => __( 'Edit Shield', 'shield' ),
		'update_item' => __( 'Update Shield', 'shield' ),
		'view_item' => __( 'View Shield', 'shield' ),
		'view_items' => __( 'View Shields', 'shield' ),
		'search_items' => __( 'Search Shield', 'shield' ),
		'not_found' => __( 'Not found', 'shield' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'shield' ),
		'featured_image' => __( 'Featured Image', 'shield' ),
		'set_featured_image' => __( 'Set featured image', 'shield' ),
		'remove_featured_image' => __( 'Remove featured image', 'shield' ),
		'use_featured_image' => __( 'Use as featured image', 'shield' ),
		'insert_into_item' => __( 'Insert into Shield', 'shield' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Shield', 'shield' ),
		'items_list' => __( 'Shields list', 'shield' ),
		'items_list_navigation' => __( 'Shields list navigation', 'shield' ),
		'filter_items_list' => __( 'Filter Shields list', 'shield' ),
	);
	$args = array(
		'label' => __( 'Shield', 'shield' ),
		'description' => __( 'Shield post type', 'shield' ),
		'labels' => $labels,
		'menu_icon' => 'dashicons-admin-customizer',
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields'),
		'taxonomies' => array('category', 'post_tag'),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => false,
		'hierarchical' => false,
		'exclude_from_search' => false,
		'show_in_rest' => true,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);
	register_post_type( 'shield', $args );

	}
			/**
		 * Adds a submenu page under a custom post type parent.
		 */
		
		 function shield_import_csv() {
		    add_submenu_page(
		        'edit.php?post_type=shield',
		        __( 'Import CSV file', 'textdomain' ),
		        __( 'Import CSV', 'textdomain' ),
		        'manage_options',
		        'import-csv-file',
		        array($this,'import_interface')
		        
		    );
		}

		
		/**
		 * Display callback for the import CSV submenu page.
		 */
		function import_interface() { 
			?>
			<div style="margin-top: 50px;">
                <form class="form-horizontal" action="" method="post" name="upload_csv" enctype="multipart/form-data">
             
                        <label>Select CSV File</label>
                            <input type="file" name="file" id="file" class="input-large">
                            <button type="submit" id="submit" name="Import" class="button button-primary button-loading" data-loading-text="Loading...">Import</button>
                </form>
            </div>
	      <?php
		}
		function upload_csv()
		{
			if(isset($_POST["Import"])){
    
    $filename=$_FILES["file"]["tmp_name"];    
     if($_FILES["file"]["size"] > 0)
     {

        $file = fopen($filename, "r");
        fgetcsv($file);
          while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
           {
           		 $status = $getData[1];
           		 $title = $getData[3];
           		 $category = $getData[4];
           		 $fimage_url = $getData[5];
           		 $image1_url = $getData[6];
           		 $image2_url = $getData[7];
           		 $shield_url = $getData[8];
           		 $shield_url_title = ltrim(str_replace($getData[4], "", $getData[3]));
           		$tags = $getData[9];
           		$taglist = explode("#",$tags);
           		
           		
           		 $maker_title = $getData[10];
           		 $maker_link = $getData[11];
           		 $desc = $getData[12];
          
           		 $o_src = $getData[13];
           		 
           		 if ($o_src == 'Y' || $o_src == 'y' || $o_src == 'yes' || $o_src == 'Yes') {
           		 	$o_src = "yes";
           		 }
           		 else if ($o_src == 'N' || $o_src == 'n' || $o_src == 'no' || $o_src == 'No') {
           		 	$o_src = "no";
           		 }
           		 else {
           		 	$o_src = "Unknown";
           		 }
           		 
           		 $lurl = $getData[14];
           		 $ltit = $getData[15];
           		 $srcl = $getData[16];
           		 $srct = $getData[17];
           		$apins = $getData[18];
           		$analogpins = explode("#",$apins);
           		
           		$dpins = $getData[19];
           		$digipins = explode("#", $dpins);
           		
           		 $pnote = $getData[20];
           		 $liscnote = $getData[21];
           		 $version =  $getData[22];
           		 $voltage =  $getData[23];
           		 $current_draw = $getData[24];
           		 $price = $getData[25];
           		 $resource_title = $getData[26];
           		 $resource_link = $getData[27];

           		 $resources = array(
           		 	'title' => $resource_title,
           		 	'url' => $resource_link,
           		 	'target' => '_blank'
           		 );

           		$s_url = array(
		        'title'   => $shield_url_title,
		        'url'   => $shield_url,
		        'target'  => '_blank'
		      );
           		$maker = array(
		        'title'   => $maker_title,
		        'url'   => $maker_link,
		        'target'  => '_blank'
		      );
           		$lisc = array(
		        'title'   => $ltit,
		        'url'   => $lurl,
		        'target'  => '_blank'
		      );
           		$source = array(
		        'title'   => $srct,
		        'url'   => $srcl,
		        'target'  => '_blank'
		      );

           	$cat_ID = get_cat_ID( $getData[4] );

				//If it doesn't exist create new category
				if($cat_ID == 0) {
					
				    $cat_name = array('cat_name' => $getData[4]);
				    wp_insert_category($cat_name);
				}
				//Get ID of category again incase a new one has been created
				$new_cat_ID = get_cat_ID($getData[4]);
           
           	// Create post object
			$post_id = wp_insert_post( array(
			    'post_title' => $getData[3],//Post Title
			    'post_type' => 'shield',
			    'post_content' => $getData[12],//Post Content
			    'post_status' => 'publish',
			    'post_author' => 1,
			    'post_category' => array($new_cat_ID),//Post Category
			     // some simple key / value array
			    'meta_input' => array(
			        'shield_url' => $s_url,
			        'tags' => $taglist,
			        'maker' => $maker,
			        'open_source' => $o_src,
			        'license' => $lisc,
			        'source' => $source,
			        'note' => $pnote,
			        'lisc_note' => $liscnote,
			        'left_pins' => $analogpins,
			        'right_pins' => $digipins,
			        'version' => $version,
			        'voltage' => $voltage,
			        'current_draw' => $current_draw,
			        'price' => $price,
			        'resources' => $resources,
			        'status' => $status
			        // and so on ;)
			    )
			));
			
			if ($post_id) {
				$custom_image1 = $this->Generate_Featured_Image( $image1_url, $post_id, $desc="" );
				if (is_wp_error($custom_image1 )) {
					$custom_image1 ="";
				}
				$custom_image2 = $this->Generate_Featured_Image( $image2_url, $post_id, $desc="");
				if (is_wp_error($custom_image2 )) {
					$custom_image2 ="";
				}
				$featured_image = $this->Generate_Featured_Image( $fimage_url, $post_id, $desc="" );
				
				add_post_meta($post_id,'custom_image1',$custom_image1);
				add_post_meta($post_id,'custom_image2',$custom_image2);
				
      	     		wp_set_post_tags( $post_id,$taglist);
			 
      	     		

			}
            
           }
      
           fclose($file);  
     }
  }   
		}
		function Generate_Featured_Image( $file, $post_id, $desc ){
			    // Set variables for storage, fix file filename for query strings.
			    preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
			    if ( ! $matches ) {
			         return new WP_Error( 'image_sideload_failed', __( 'Invalid image URL' ) );
			    }

			    $file_array = array();
			    $file_array['name'] = basename( $matches[0] );

			    // Download file to temp location.
			    $file_array['tmp_name'] = download_url( $file );

			    // If error storing temporarily, return the error.
			    if ( is_wp_error( $file_array['tmp_name'] ) ) {
			        return $file_array['tmp_name'];
			    }

			    // Do the validation and storage stuff.
			    $id = media_handle_sideload( $file_array, $post_id, $desc );

			    // If error storing permanently, unlink.
			    if ( is_wp_error( $id ) ) {
			        @unlink( $file_array['tmp_name'] );
			        return $id;
			    }
			    if (set_post_thumbnail( $post_id, $id ))
			    {
			        $title = get_the_title( $post_id )." Preview";
			        update_post_meta($id, '_wp_attachment_image_alt', $title);
			    	return $id;
			    }

			}
		
}



if ( class_exists( 'Shield' ) ) {
	//instantiating class object
	$s = new Shield();
	//calling the register function that execute all the action hooks specified inside the register function.
	$s ->register();
}
// activation
register_activation_hook( __FILE__, array( $s, 'activate' ) );
// deactivation
register_deactivation_hook( __FILE__, array( $s, 'deactivate' ) );

  
