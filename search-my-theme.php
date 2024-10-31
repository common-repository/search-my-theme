<?php
/*
Plugin Name: Search My Theme
Plugin URI: http://wordpress.org/extend/plugins/search-my-theme/
Description: A wordpress plugin that lets you search for text within your templates.

Installation:

1) Install WordPress 5.6 or higher

2) Download the latest from:

http://wordpress.org/extend/plugins/search-my-theme

3) Login to WordPress admin, click on Plugins / Add New / Upload, then upload the zip file you just downloaded.

4) Activate the plugin.

Version: 3.2.1
Author: TheOnlineHero - Tom Skroza
License: GPL2
*/

if (!class_exists("SMTTomM8")) {
  include_once("lib/tom-m8te.php");
}

add_action('admin_menu', 'register_search_my_theme_page');
function register_search_my_theme_page() {
	add_menu_page('Search My Theme', 'Search My Theme', 'manage_options', 'search-my-theme/search-my-theme.php', 'search_my_theme_page');
}

function search_my_theme_page() { ?>
	<div class="wrap">
  <h2>Search My Theme</h2>
  <?php
		if ($_POST["search_text"] != "") {
			$_POST["search_text"] = sanitize_text_field($_POST["search_text"]);
			$search_text = str_replace('\"', "\"", $_POST["search_text"]);
			$search_text = str_replace("\'", '\'', $search_text);
      ob_start();
      search_my_theme_search_text(get_template_directory(), $search_text);
      $search_results = ob_get_contents();
      ob_end_clean();

      if ($search_results != "") {
        echo("<div id='message' class='updated below-h2'><p>The search word &#8216;$search_text&#8217; is found in these files.</p></div><ul>$search_results</ul>");
      } else {
        echo("<div id='message' class='updated below-h2'><p>Sorry, no results. Try again.</p></div>");
      }
		} else {
      echo("<div id='message' class='updated below-h2'><p>Please type in a keyword, before searching.</p></div>");
    }
		?>
		<p>To search for text within your current themes directory, simply type in the text and click the Search button.</p>
		<form action="" method="post">
			<?php 
				SMTTomM8::add_form_field(null, "text", "Search", "search_text", "search_text", array(), "p", array());
			?>
			<p><input type="submit" name="action" value="Search" /></p>
		</form>
		<?php
	    SMTTomM8::add_social_share_links("http://wordpress.org/extend/plugins/search-my-theme/");
	?>
	</div>
	<?php
}

function search_my_theme_search_text($src, $search_text) { 
    $dir = opendir($src); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
              search_my_theme_search_text($src . '/' . $file, $search_text);
            } else {
            	$content = file_get_contents($src . '/' . $file);
							if (@strpos($content, $search_text, 1)) {
								$short_slug = str_replace(get_template_directory()."/", "", $src . '/' . $file);
								if (@strpos($short_slug, "/", 1)) {	
									echo("<li>".$src . '/' . $file."</li>");
								} else {
									echo("<li><a target='_blank' href='".get_option("siteurl")."/wp-admin/theme-editor.php?file=".$short_slug."&theme=".wp_get_theme()->Template."'>");
									echo($short_slug);
		            	echo("</a></li>");
								}
							}
            }
        }   
    }
    closedir($dir); 
}

?>