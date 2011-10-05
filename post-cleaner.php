<?php
/*
Plugin Name: Post Content Cleaner
Plugin URI: http://www.hebeisenconsulting.com
Description:  Clean up unwanted P, DIV, SPAN, tag parameters, multiple spaces and \n characters. Using the plugin as a filter avoids permanent changes to posts.
Version: 1.1
Author: Hebeisen Consulting - R Bueno
Author URI: http://www.hebeisenconsulting.com
License: A "Slug" license name e.g. GPL2
*/
/*  Copyright 2011 Hebeisen Consulting

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Administrator menu
add_action('admin_menu', 'post_clean_menu');

//Define plugin path
define('WP_PLUGIN_URL', ABSPATH . 'wp-content/plugins/post-clean');

//plugin installation
//create ew table upon activating plugin
function post_clean_install()
{
    global $wpdb;
    $table = $wpdb->prefix . "post_clean";
	if($wpdb->get_var("show tables like '$table'") != $table) 
		{   			
		    $sql = "CREATE TABLE " . $table . " (
						  id int(11) NOT NULL AUTO_INCREMENT,
						  html_tag varchar(150) NOT NULL,
						  html_tag_simplify varchar(150) NOT NULL,					  
						  allowed_tag INT(1) NOT NULL,
						  allowed_param INT(1) NOT NULL,
						  strip_type varchar(10) NOT NULL,
						  replacement varchar(100) NOT NULL,
						  PRIMARY KEY (id)
						)";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		    dbDelta($sql);
		  }
	
}

function post_clean_install_add_data()
{
	global $wpdb;
	$table = $wpdb->prefix . "post_clean";
	//add data
	//div
	$wpdb->insert( $table, array( 'id' => '1', 'html_tag' => '<div>', 'html_tag_simplify' => 'div', 'allowed_tag' => '1', 'allowed_param' => '1', 'strip_type' => '', 'replacement' => '' ) );
	//p
	$wpdb->insert( $table, array( 'id' => '2', 'html_tag' => '<p>', 'html_tag_simplify' => 'p', 'allowed_tag' => '1', 'allowed_param' => '1', 'strip_type' => '', 'replacement' => ''  ) );
	//span
	$wpdb->insert( $table, array( 'id' => '3', 'html_tag' => '<span>', 'html_tag_simplify' => 'span', 'allowed_tag' => '1', 'allowed_param' => '1', 'strip_type' => '', 'replacement' => ''  ) );
	//$nbsp;
	$wpdb->insert( $table, array( 'id' => '4', 'html_tag' => '$nbsp;', 'html_tag_simplify' => '$nbsp;', 'allowed_tag' => '1', 'allowed_param' => '1', 'strip_type' => '', 'replacement' => ''  ) );	
	//filter type
	$wpdb->insert( $table, array( 'id' => '5', 'html_tag' => '', 'html_tag_simplify' => '', 'allowed_tag' => '0', 'allowed_param' => '1', 'strip_type' => 'filter', 'replacement' => ''  ) );
	//BR;
	$wpdb->insert( $table, array( 'id' => '6', 'html_tag' => 'br', 'html_tag_simplify' => 'br', 'allowed_tag' => '1', 'allowed_param' => '0', 'strip_type' => '', 'replacement' => ''  ) );
	//\n;
	$wpdb->insert( $table, array( 'id' => '7', 'html_tag' => '\n', 'html_tag_simplify' => '\n', 'allowed_tag' => '0', 'allowed_param' => '1', 'strip_type' => '', 'replacement' => ''  ) );

}
register_activation_hook(__FILE__,'post_clean_install');
register_activation_hook(__FILE__,'post_clean_install_add_data');


function post_clean_deactivate()
{
	global $wpdb;
	$table = $wpdb->prefix . "post_clean";
	if($wpdb->get_var("show tables like '$table'") == $table) 
		{
			//$sql = "DROP TABLE IF EXISTS". $table;
			$wpdb->query("DROP TABLE IF EXISTS $table");
		}	
}

register_deactivation_hook(__FILE__, 'post_clean_deactivate' );

//Wordpress admin menu
function post_clean_menu()
{
	$page = add_options_page('Post Content Cleaner', 'Post Content Cleaner', 'manage_options', 'post-cleaner', 'post_cleaner_option');
	
	//add action to insert javascript in admin page <head> only when plugin is trigerred
	add_action("admin_print_scripts-" . $page, "post_clean_head");
}

//Javascript action in theme header for check box ticking.
function post_clean_head()
{
?>
<script language="JavaScript">
	function CheckBoxesByName(sName,sMode)
		{
		var boxes = document.getElementsByName(sName);
		var i=0;
		        
		  // For each checkbox with name sName
		  for (i=0;i<=boxes.length;i++)
		  {
		    // Make sure we are dealing with checkboxes
		    if (boxes[i].type=="checkbox") {
		      switch (sMode) {
		      case 0:
		        // Uncheck checkbox
		        boxes[i].checked = false;    
		        break;
		      case 1:
		        // Check checkbox
		        boxes[i].checked = true;    
		        break;
		      case 2:
		        // Toggle checkbox
		        if (boxes[i].checked == true) {
		          boxes[i].checked = false; 
		        } else {
		          boxes[i].checked = true; 
		        }  
		        break;   
		      }
		    }
		  }
		}
		
	function checkDIV()
	{
		//var boxes = document.getElementsByName(sName);
		if (document.getElementsByName("yes")[0].checked == true)
		{
			document.getElementsByName("no")[0].checked = true;
		}	
	}
	function uncheckDIV(varName)
	{
		if (document.getElementsByName(varName)[0].checked == true)
		{
			//alert('hi');
			//document.getElementsByName(sName)[0].checked == false;
			document.getElementsByName("no")[0].checked = true;
			//document.getElementsByName(sName)[0].checked == false;
		}
	}
	
	function checkP()
	{
		//var boxes = document.getElementsByName(sName);
		if (document.getElementsByName("yesP")[0].checked == true)
		{
			document.getElementsByName("noP")[0].checked = true;
		}	
	}
	function uncheckP(varName)
	{
		if (document.getElementsByName(varName)[0].checked == true)
		{
			//alert('hi');
			//document.getElementsByName(sName)[0].checked == false;
			document.getElementsByName("noP")[0].checked = true;
			//document.getElementsByName(sName)[0].checked == false;
		}
	}
	
	function checkSPAN()
	{
		//var boxes = document.getElementsByName(sName);
		if (document.getElementsByName("yesSPAN")[0].checked == true)
		{
			document.getElementsByName("noSPAN")[0].checked = true;
		}	
	}
	function uncheckSPAN(varName)
	{
		if (document.getElementsByName(varName)[0].checked == true)
		{
			//alert('hi');
			//document.getElementsByName(sName)[0].checked == false;
			document.getElementsByName("noSPAN")[0].checked = true;
			//document.getElementsByName(sName)[0].checked == false;
		}
	}

	function checkBR()
	{
		//var boxes = document.getElementsByName(sName);
		if (document.getElementsByName("yesBR")[0].checked == true)
		{
			document.getElementsByName("noBR")[0].checked = true;
		}	
	}
	function uncheckBR(varName)
	{
		if (document.getElementsByName(varName)[0].checked == true)
		{
			//alert('hi');
			//document.getElementsByName(sName)[0].checked == false;
			document.getElementsByName("noBR")[0].checked = true;
			//document.getElementsByName(sName)[0].checked == false;
		}
	}
</script>
<?php
}

//post cleaner options under menu
function post_cleaner_option(){

	global $wpdb;

	if (isset($_POST['post_clean']))
	{
		//strip all div
		if ( isset( $_POST['yes'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET allowed_tag = '0', allowed_param = '1' WHERE id = '1'" );
				echo '<div id="message" class="updated fade"><p>All DIV tags have been stripped.</p></div>';
			}
		//strip param inside div
		if ( !isset( $_POST['yes'] ) && isset( $_POST['no'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET allowed_tag = '1', allowed_param = '0' WHERE id = '1'" );
				echo '<div id="message" class="updated fade"><p>All parameters in DIV tags have been stripped.</p></div>';
			}
		//revert back values to 1 if not isset both strip tag and param
		//Div
		if ( !isset( $_POST['yes'] ) && !isset( $_POST['no'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET allowed_tag = '1', allowed_param = '1' WHERE id = '1'" );
			}
		//strip P
		if ( isset( $_POST['yesP'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET allowed_tag = '0', allowed_param = '1' WHERE id = '2'" );
				echo '<div id="message" class="updated fade"><p>All P tags have been stripped.</p></div>';
			}
		//strip all param inside P
		if ( !isset( $_POST['yesP'] ) && isset( $_POST['noP'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET allowed_tag = '1', allowed_param = '0' WHERE id = '2'" );
				echo '<div id="message" class="updated fade"><p>All parameters in P tags have been stripped.</p></div>';
			}
		//revert back values to 1 if not isset both strip tag and param
		//P
		if ( !isset( $_POST['yesP'] ) && !isset( $_POST['noP'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET allowed_tag = '1', allowed_param = '1' WHERE id = '2'" );
			}
		//strip Span	
		if ( isset( $_POST['yesSPAN'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET allowed_tag = '0', allowed_param = '1' WHERE id = '3'" );
				echo '<div id="message" class="updated fade"><p>All SPAN tags have been stripped.</p></div>';
			}
		//strip all param inside span
		if ( !isset( $_POST['yesSPAN'] ) && isset( $_POST['noSPAN'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET allowed_tag = '1', allowed_param = '0' WHERE id = '3'" );
				echo '<div id="message" class="updated fade"><p>All parameters in SPAN tags have been stripped.</p></div>';
			}
		//revert back values to 1 if not isset both strip tag and param
		//SPAN
		if ( !isset( $_POST['yesSPAN'] ) && !isset( $_POST['noSPAN'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET allowed_tag = '1', allowed_param = '1' WHERE id = '3'" );
			}
		//strip nbsp;	
		if ( isset( $_POST['strip-nbsp'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET allowed_tag = '0', allowed_param = '1' WHERE id = '4'" );
				echo '<div id="message" class="updated fade"><p>All &#38;nbsp&#59; tags have been stripped.</p></div>';
			}	
		//revert back values to 1 if not isset both strip tag and param
		//nbsp;
		if ( !isset( $_POST['strip-nbsp'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET allowed_tag = '1', allowed_param = '1' WHERE id = '4'" );
			}
		//strip white spaces	
		/*if ( isset( $_POST['strip-nbsp'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET allowed_tag = '0', allowed_param = '1' WHERE id = '4'" );
				echo '<div id="message" class="updated fade"><p>All &#38;nbsp&#59; tags have been stripped.</p></div>';
			}
			*/		
		// two br's have are checked
		if ( isset( $_POST['yesBR'] ) && isset($_POST['noBR']) )
			{
				echo '<div id="message" class="updated fade"><p>Only one choice is allowed.</p></div>';
			}
		else
			{
				// replace br with new line
				if ( isset( $_POST['yesBR'] ) )
					{
						$wpdb->query( "UPDATE wp_post_clean SET replacement = 'new_line' WHERE id = '6'" );
						echo '<div id="message" class="updated fade"><p>All BR tags has been replaced by \n.</p></div>';
					}
				// replace br with space
				if ( isset( $_POST['noBR'] ) )
					{
						$wpdb->query( "UPDATE wp_post_clean SET replacement = 'space' WHERE id = '6'" );
						echo '<div id="message" class="updated fade"><p>All BR tags has been replaced by space.</p></div>';
					}
			}
		//all br's have not been set
		if ( !isset( $_POST['yesBR'] ) && !isset($_POST['noBR']) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET replacement = '' WHERE id = '6'" );
			}
		// replace \n with space
		if ( isset( $_POST['replace-n-with-space'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET replacement = 'space' WHERE id = '7'" );
				echo '<div id="message" class="updated fade"><p>All \n tags has been replaced by space.</p></div>';
			}
		// revert changes of \n
		if ( !isset( $_POST['replace-n-with-space'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET replacement = '' WHERE id = '7'" );
			}
		//strip type filter
		if ( isset( $_POST['filter-only'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET strip_type = 'filter' WHERE id = '5'" );
			}
		//strip type clean
		if ( isset( $_POST['clean-post'] ) )
			{
				$wpdb->query( "UPDATE wp_post_clean SET strip_type = 'clean' WHERE id = '5'" );
			}
	}
	switch($_GET['page']){
		case 'post-cleaner':
		global $wpdb;	
			$allowed_html_tags = $wpdb->get_col("SELECT html_tag_simplify FROM wp_post_clean WHERE allowed_tag = 1");
			$html_tags = implode(", ", $allowed_html_tags);	
			
			$strip_type = $wpdb->get_results("SELECT * FROM wp_post_clean WHERE id = 5;");
				foreach($strip_type as $strip_type)
					{
						$strip_type = $strip_type->strip_type;
					}
			$rplcmnt_br = $wpdb->get_row("SELECT * FROM wp_post_clean WHERE id = 6");
			//echo $rplcmnt_br->replacement;
?>
			<div class="wrap">
			
		 	  <h2>Welcome to Post Content Cleaner</h2>
		 	  
		 	 <!--<div class="postbox">
		 	  <h3>Current Settings</h3>
		 	  <p style = "padding: 10px;"><b>Allowed Tags</b>: <?php echo $html_tags; ?></p>
		 	  <p style = "padding: 10px;"><b>Strip Method</b>: <?php if($strip_type == "filter"){ echo "Apply stripping as a filter only."; }else{ echo "Stripping has been performed on database post data."; }; ?></p>
		 	 </div>-->
		 	 
		 	 <div class="postbox"  style = "padding: 10px;">
			  <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
			  <input type="hidden" name="post_clean" id="info_update1" value="true" />
			  <table width = "100%">
			  
			   <tr>
			    <td><h4>&#60;DIV&#62; Tags</h4></td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "yes" value = "strip-entire-div" onchange="checkDIV()"> Strip the entire tag</td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "no" value = "strip-param-div" onchange="uncheckDIV('yes')"> Strip the tag&#39;s parameters only</td>
			   </tr>
			   
			  <tr>
			    <td><h4>&#60;P&#62; Tags</h4></td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "yesP" value = "strip-entire-p" onchange="checkP()"> Strip the entire tag</td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "noP" value = "strip-param-p" onchange="uncheckP('yesP')"> Strip the tag&#39;s parameters only</td>
			   </tr>
			   
			   <tr>
			    <td><h4>&#60;SPAN&#62; Tags</h4></td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "yesSPAN" value = "strip-entire-span" onchange="checkSPAN()"> Strip the entire tag</td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "noSPAN" value = "strip-param-span" onchange="uncheckSPAN('yesSPAN')"> Strip the tag&#39;s parameters only</td>
			   </tr>
			   
			   <tr>
			    <td><h4> Tags</h4></td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "strip-nbsp" value = "strip-entire-nbsp" > Replace &#38;nbsp&#59; characters with a ' ' space.</td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "strip-white-space" value = "strip-entire-space" > Replace multiple ' ' spaces with a single ' ' space.</td>
			   </tr>

			   <tr>
			    <td><h4>Breaks</h4></td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "yesBR" value = "strip-entire-nbsp"> Replace &#60;BR&#62; and its variations with a \n.</td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "noBR" value = "strip-entire-space"> Replace &#60;BR&#62; and its variations with a space.</td>
			   </tr>

			   <tr>
			    <td><h4>Newline</h4></td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "replace-n-with-space" value = "strip-entire-space" > Replace \n and its variations with a space.</td>
			   </tr>
			   
			   <tr>
			    <td><h4>General Settings</h4></td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "filter-only" value = "filter-only" onchange="CheckBoxesByName('clean-post', 2)" checked> Apply stripping as a filter only (Recommended - This does not touch the original posts content, it simply alters how it is rendered on the loaded page.)</td>
			   </tr>
			   <tr>
			    <td style="padding-left:30px;"><input type = "checkbox" name = "clean-post" value = "clean-post" onchange="CheckBoxesByName('filter-only', 2)" > Perform stripping on post data in database (Warning - not reversible. This will perform the above fixes on the post data in the database. The plugin can then be removed or disabled after the stripping is complete.)</td>
			   </tr>			   
			   
			   <tr>
			    <td colspan = "4" style="padding-top:20px;"><input type="submit" class="button-primary" value = "Submit"></td>
			   </tr>
			  </table>
			  </form>

			 </div>
			 
			</div>
<?php
		break;
	}
}
function html_strip( $content )
{	
	global $wpdb;
	
	/*
	Check table, select tags that are allowed. 
	NOTE: Default html tags are allowed.
	*/
	$allowed_html_tags = $wpdb->get_col("SELECT html_tag FROM wp_post_clean WHERE allowed_tag = 1");
	$html_tags = implode(",", $allowed_html_tags);
	
	//perform html tags stripping
	$PostClean_stripped = strip_tags($content, $html_tags);
	
	//check if $nbsp; is not allowed
	$allowed_nbsp = $wpdb->query("SELECT * FROM wp_post_clean WHERE allowed_tag = 0 AND id = 4");
	if($allowed_nbsp)
		{
			$PostClean_stripped = preg_replace("/\[(.*?)\]\s*(.*?)\s*\[\/(.*?)\]/", "", html_entity_decode($PostClean_stripped));
		}
	
	//check if br has replacement
	$rplcmnt_br = $wpdb->get_row("SELECT * FROM wp_post_clean WHERE id = 6");
	if( $rplcmnt_br->replacement == "space")
		{
			$PostClean_stripped = preg_replace("/<br>/i", " ", html_entity_decode($PostClean_stripped));
			$PostClean_stripped = preg_replace("/<br\/>/i", " ", html_entity_decode($PostClean_stripped));
			$PostClean_stripped = preg_replace("/<br \/>/i", " ", html_entity_decode($PostClean_stripped));
			$PostClean_stripped = preg_replace("/<BR>/i", " ", html_entity_decode($PostClean_stripped));
			$PostClean_stripped = preg_replace("/<BR\/>/i", " ", html_entity_decode($PostClean_stripped));
			$PostClean_stripped = preg_replace("/<BR \/>/i", " ", html_entity_decode($PostClean_stripped));
		}
	if( $rplcmnt_br->replacement == "new_line")
		{
			$PostClean_stripped = preg_replace("/<br>/i", "\n", html_entity_decode($PostClean_stripped));
			$PostClean_stripped = preg_replace("/<br\/>/i", "\n", html_entity_decode($PostClean_stripped));
			$PostClean_stripped = preg_replace("/<br \/>/i", "\n", html_entity_decode($PostClean_stripped));
			$PostClean_stripped = preg_replace("/<BR>/i", "\n", html_entity_decode($PostClean_stripped));
			$PostClean_stripped = preg_replace("/<BR\/>/i", "\n", html_entity_decode($PostClean_stripped));
			$PostClean_stripped = preg_replace("/<BR \/>/i", "\n", html_entity_decode($PostClean_stripped));
		}
	
	//check if \n has replacement
	$rplcmnt_n = $wpdb->get_row("SELECT * FROM wp_post_clean WHERE id = 7");
	if( $rplcmnt_br->replacement == "space")
		{		
			$PostClean_stripped = preg_replace("/\n/i", " ", html_entity_decode($PostClean_stripped));
			$PostClean_stripped = preg_replace("/\r\n/i", " ", html_entity_decode($PostClean_stripped));
		}
	
	//strip all white space
	$PostClean_stripped = preg_replace('/\s\s+/', "", $PostClean_stripped);
	
	/*
	Check table, select allowed tag attributes that are allowed. 
	NOTE: Default html tags are allowed.
	*/
	$allowed_param_tags = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM wp_post_clean WHERE allowed_param = 0;"));
	
	if($allowed_param_tags == "0")
		{
			return $PostClean_stripped;
		}
	if($allowed_param_tags == "1")
		{
			$id = $wpdb->get_row("SELECT * FROM wp_post_clean WHERE allowed_param = 0;");
			$param[] = $id->html_tag_simplify;
			$PostClean = preg_replace("/<(".$param[0].")[^>]*?(\/?)>/i",'<$1$2>', $PostClean_stripped );
			return $PostClean;
		}
		
	if($allowed_param_tags == "2")
		{
			$id = $wpdb->get_results("SELECT * FROM wp_post_clean WHERE allowed_param = 0;");
			foreach($id as $id)
				{
					$param[] = $id->html_tag_simplify;
				}
				$PostClean_1 = preg_replace("/<(".$param[0].")[^>]*?(\/?)>/i",'<$1$2>', $PostClean_stripped );
				$PostClean = preg_replace("/<(".$param[1].")[^>]*?(\/?)>/i",'<$1$2>', $PostClean_1 );
				return $PostClean;
				
		}
	
	if($allowed_param_tags == "3")
		{
			$id = $wpdb->get_results("SELECT * FROM wp_post_clean WHERE allowed_param = 0;");
			foreach($id as $id)
				{
					$param[] = $id->html_tag_simplify;
				}
				$PostClean_1 = preg_replace("/<(".$param[0].")[^>]*?(\/?)>/i",'<$1$2>', $PostClean_stripped );
				$PostClean_2 = preg_replace("/<(".$param[1].")[^>]*?(\/?)>/i",'<$1$2>', $PostClean_1 );
				$PostClean = preg_replace("/<(".$param[2].")[^>]*?(\/?)>/i",'<$1$2>', $PostClean_2 );
				return $PostClean;
				
		}
	if($allowed_param_tags == "4")
		{
			$id = $wpdb->get_results("SELECT * FROM wp_post_clean WHERE allowed_param = 0;");
			foreach($id as $id)
				{
					$param[] = $id->html_tag_simplify;
				}
				$PostClean_1 = preg_replace("/<(".$param[0].")[^>]*?(\/?)>/i",'<$1$2>', $PostClean_stripped );
				$PostClean_2 = preg_replace("/<(".$param[1].")[^>]*?(\/?)>/i",'<$1$2>', $PostClean_1 );
				$PostClean_3 = preg_replace("/<(".$param[2].")[^>]*?(\/?)>/i",'<$1$2>', $PostClean_2 );
				$PostClean = preg_replace("/<(".$param[3].")[^>]*?(\/?)>/i",'<$1$2>', $PostClean_3 );
				return $PostClean;
				
		}
		
}

/*
check if post filter or post clean
post filter = perform stripping after saving
post clean = perform stripping before saving
ID 5 is the column for filtering options
*/

global $wpdb;
$html = $wpdb->get_results("SELECT * FROM wp_post_clean WHERE id = 5;");
	foreach($html as $html)
		{
			$strip_type = $html->strip_type ;
		}
if ($strip_type == "clean")
	{				
		add_filter('content_save_pre', 'html_strip');
	}
if ($strip_type == "filter")
	{
		add_filter('the_content', 'html_strip');
	}

?>