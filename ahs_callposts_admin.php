<?php
$ahscp_options = array (
	array("name" => "Global Settings",
		"id" => "ahscp_global_settings",
		"type" => "header3"),

	array("name" => "Title Style",
		"id" => "ahscp_titletype",
		'type' => 'text',
		'size'=>50,
		'std'=>'h3',
		'help'=>'This is the HTML tag used for post titles in the shortcode. You can override this default setting in any individual use of the shortcode by including the option <code>title</code>. <i>(Ignored for instances of the shortcode using a template.)</i>'),

	array("name" => "Content Style",
		"id" => "ahscp_contentstyle",
		'type'=>'select',
		'options'=>array('full'=>'full','excerpt'=>'excerpt','title'=>'title'),
		'help'=>'This controls whether and how each post\'s content is displayed. You can override this default setting in any individual use of the shortcode by including the option <code>content_style</code>. <i>(Ignored for instances of the shortcode using a template.)</i>'),

	array("name" => "Date Format",
		"id" => "ahscp_dateformat",
		'type' => 'text',
		'std'=>'',
		'size'=>100,
		'help'=>'If you enter a date format here, it will be used to insert the post date for all posts on the site. You can override this default setting in any individual use of the shortcode by including the option <code>dateformat</code> and setting it to a different format or to empty.<br />A nice default is <code>F j</code>. See PHP.net\'s <a href="http://php.net/manual/en/function.date.php" target="_blank">Date Formatting Instructions</a> for more.'),

	array("name" => "Order by",
		"id" => "ahscp_orderby",
		'type'=>'select',
		'options'=>array('post_date'=>'Post Date','rand'=>'Random',),
		'help'=>'Usually, you want it to be by the date of the post, but you can choose to make it random. You can override this default setting in any individual use of the shortcode by including the option <code>orderby</code>.'),

	array("name" => "Styling",
		"id" => "ahscp_css",
		'std'=>'',
		'type'=>'textarea',
		'help'=>'When you first install this plugin, this box will have the default css that I put in, mostly to style the 2-column and sidebox options. You can edit this css easily here.'),

	array("name" => "Custom Field",
		"id" => "ahscp_customfield",
		'type' => 'text',
		'std'=>'',
		'size'=>150,
		'help'=>'The shortcode automatically orders posts by most-recent-first. If you\'d like them to be ordered by a custom field, enter the name of the custom field here. You can override this default setting in any individual use of the shortcode by including the option <code>custom_field</code>. If you set a custom_field value here, but want it to NOT impact a particular shortcode instances, set its value to <code>false</code>.'),

	/* Templates tab */
	array("name" => "Templates",
		"id" => "ahscp_templates",
		"type" => "header3"),

	array("name" => "Number of templates",
		"id" => "ahscp_tmplnum",
		"type" => "select",
		'options'=>range(0,10),
		),
);

for ($i=1;$i<=get_option('ahscp_tmplnum');$i++) {
	$ahscp_options[] = array(
		'id'=>'ahscp_tmpl_'.$i,
		'name'=>'Template #'.$i,
		'type'=>'header4',
	);
	$ahscp_options[] = array(
		'id'=>'ahscp_tmpl_'.$i.'_title',
		'name'=>'Name',
		'type'=>'text',
		'std'=>'Template #'.$i,
	);
	$ahscp_options[] = array(
		"id" => 'ahscp_tmpl_'.$i.'_group',
		"name" => "Grouping Type",
		"type" => "select",
		'options'=>array('&lt;div&gt;','&lt;ul&gt;'),
		'help'=>'Put the posts, each of which will use the template below, into a parent &lt;div&gt; or a parent &lt;ul&gt;?',
	);
	$ahscp_options[] = array(
		'id'=>'ahscp_tmpl_'.$i.'_text',
		'name'=>'Text',
		'type'=>'textarea'
	);
}


$ahscp_options[] = array("name" => "Template Tags",
	"id" => "ahscp_tmpltags",
	'type'=>'header4',
	'help'=>'
<code>%%TITLE%%</code> - post title<br />
<code>%%URL%%</code> - post permalink<br />
<code>%%IMAGE%%</code> - post thumbnail image<br />
<code>%%DATE%%</code> - post date<br />
<code>%%CONTENT%%</code> - post content<br />
<code>%%CONTENTNOIMG%%</code> - post content with images removed<br />
<code>%%EXCERPT%%</code> - post excerpt<br />
<code>%%EDITLINK%%</code> - link to edit the post (displayed as tiny pencil)<br />
<code>%%CATEGORY%%</code> - the (first) category you used in the shortcode
	',
);



function ahscp_add_admin() {
    global $ahscp_options;

    // add hidden field to store last-used tab
    $ahscp_options[] = array('id'=>'ahscp_activetab', 'name'=>'activetab', 'type'=>'hidden','divclass'=>'hidden','std'=>2);

    if ( isset($_GET['page']) && $_GET['page'] == basename(__FILE__) ) {
        if ( isset($_REQUEST['action']) && 'save' == $_REQUEST['action'] ) {
                foreach ($ahscp_options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
                foreach ($ahscp_options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
                header("Location: options-general.php?page=".basename(__FILE__)."&saved=true");
                die;
        } else if ( isset($_REQUEST['action']) && 'reset' == $_REQUEST['action'] ) {
            foreach ($ahscp_options as $value) delete_option( $value['id'] );
            header("Location: options-general.php?page=".basename(__FILE__)."&reset=true");
            die;
        }
    }
	add_submenu_page('options-general.php', 'April\'s Call Posts', 'April\'s Call Posts', 'edit_theme_options', basename(__FILE__), 'ahscp_adminpage');
}

function ahscp_adminpage() {
    global $ahscp_options;
    if ( isset($_REQUEST['saved']) && $_REQUEST['saved'] ) $msg = '<div class="updated" style="width: 785px;"><p>Call Posts settings saved.</p></div>';
    if ( isset($_REQUEST['reset']) && $_REQUEST['reset'] ) $msg = '<div class="updated" style="width: 785px;"><p>Call Posts settings reset.</p></div>';
?>
<div class="wrap ahscp" id="backtotop" style="width: 800px;">

<h2>April's Call Posts - Documentation &amp; Settings</h2>
<p>Built by Aaron Silver of <a href="http://springthistle.com/" target="_blank">Springthistle Tech</a></p>


<?php if (isset($msg)) echo $msg; ?>

<form method="post">

<div id="tabs">

<?php

$tabs = array();
$cards = array();
$numtabs = 0;

foreach ($ahscp_options as $value) {

if (preg_match('/^header([0-9])$/',$value['type'],$matches)) {
	if (preg_match('/^header3$/',$value['type'])) {
		$numtabs++;
		$tabs[$numtabs] = $value['name'];
	}
	if (!isset($cards[$numtabs])) $cards[$numtabs] = '';
	$cards[$numtabs] .= '<h'.$matches[1].'>'.$value['name'].'</h'.$matches[1].'>';
	if (!empty($value['help'])) $cards[$numtabs] .=  $value['help'];
} else {

	$cards[$numtabs] .= '<div class="input';
	if (isset($value['divclass'])) $cards[$numtabs] .= ' '.$value['divclass'];
	$cards[$numtabs] .= '">';
	$cards[$numtabs] .= '<label>'.$value['name'].'</label>';

	switch ($value['type']) {

		case 'select':
			$cards[$numtabs] .= '<div id="td-'.$value['id'].'">';
			$cards[$numtabs] .= '<select name="'.$value['id'].'" id="'.$value['id'].'">';
			foreach ($value['options'] as $optid=>$option) {
				$cards[$numtabs] .= '<option value="'.$optid.'" ';
				if (get_option($value['id']) == $optid) $cards[$numtabs] .= ' selected="selected"';
				elseif (isset($value['std']) && $option == $value['std']) $cards[$numtabs] .= ' selected="selected"';
				$cards[$numtabs] .= '>'.$option.'</option>';
			}
			$cards[$numtabs] .= '</select></div>';
			break;
		case 'textarea':
			$cards[$numtabs] .= '<textarea name="'.$value['id'].'" id="'.$value['id'].'" rows="';
			if (!empty($value['rows'])) $cards[$numtabs] .= $value['rows'];
			else $cards[$numtabs] .= '5';
			$cards[$numtabs] .= '">';
			if ( get_option( $value['id'] ) != "") $cards[$numtabs] .= stripslashes(get_option($value['id']));
			else $cards[$numtabs] .= stripslashes($value['std']);
			$cards[$numtabs] .= '</textarea>';
			break;
		case 'hidden':
			$cards[$numtabs] .= '<input name="'.$value['id'].'" id="'.$value['id'].'" type="hidden" value="';
			if ( get_option( $value['id'] ) != "") $cards[$numtabs] .= get_option( $value['id'] );
			else $cards[$numtabs] .= $value['std'];
			$cards[$numtabs] .= '" />';
			break;
		default:
			$cards[$numtabs] .= '<input name="'.$value['id'].'" id="'.$value['id'].'" type="text" value="';
			if ( get_option( $value['id'] ) != "") $cards[$numtabs] .= get_option( $value['id'] );
			else $cards[$numtabs] .= $value['std'];
			$cards[$numtabs] .= '" style="';
			if (!empty($value['size'])) $cards[$numtabs] .= 'width:'.$value['size'].'px';
			$cards[$numtabs] .= '" />';
	} // endswitch

	if (!empty($value['help'])) $cards[$numtabs] .=  '<div class="help">'.$value['help'].'</div>';
	$cards[$numtabs] .=  '<div class="clrleft"></div></div><!--/ class=input -->';
}
}

echo '<ul class="thetabs">';
echo '<li id="tab-0"><a href="#card-0">Documentation</a></li>';
foreach ($tabs as $i=>$t) {
	echo '<li id="tab-'.$i.'"><a href="#card-'.$i.'">'.$t.'</a></li>';
}
echo '</ul>';

echo '<div class="card" id="card-0">';
include('readme.html');
echo '<div class="clr"></div></div>';

foreach ($cards as $i=>$c) {
	echo '<div class="card" id="card-'.$i.'">'.$c.'<div class="clr"></div></div>';
}

?>

</div><!--/ tabs -->

<div style="float:right;">
	<input name="save" type="submit" class="button button-primary" value="Save changes on all tabs" />
	<input type="hidden" name="action" value="save" />
</div>
</form>
<form method="post" style="float:left;">
	<input name="reset" type="submit" class="button" value="<?php _e('Delete all Data and Reset to Default Settings'); ?>" />
	<input type="hidden" name="action" value="reset" />
</form>

<script type="text/javascript">
jQuery(document).ready(function($){
	$('#tabs div.card').hide();
	<?php
	    if (isset($_REQUEST['saved']) && $_REQUEST['saved']) {
			$tmp = get_option('ahscp_activetab');
		}
		if (empty($tmp)) $tmp = 0;
	?>;
	var activetab = <?php echo $tmp; ?>;
	$('#tabs #card-'+activetab).show();
	$('#tabs #tab-'+activetab).addClass('active');
	$('#tabs ul li a').click(function(){
		$('#tabs ul li').removeClass('active');
		$(this).parent().addClass('active');
		var currentTab = $(this).attr('href');
		$('#tabs div.card').hide();
		$(currentTab).show();
		var tabnum = currentTab.substr(6,1); // extract number from tab #id (e.g. #tab-3)
		$('#ahscp_activetab').val(tabnum);
		$('.updated').remove();
		return false;
	});
	$('#showmore').click(function(){
		$('#morerow').remove();
		$('#morestuff').show();
	});
});
</script>


<?php
} // end function ahscp_admin()

function ahscp_admin_head() {
	echo '<link href="'.plugin_dir_url(__FILE__).'ahscpstyle.css" rel="stylesheet" type="text/css" />';
}
add_action('admin_head', 'ahscp_admin_head');

// Set the default options when the plugin is activated
function ahs_callposts_activate(){
    add_option('ahscp_css', ".callposts_2col .post { float: left; margin-right: 20px; border-bottom: 1px solid #999; overflow: hidden; clear: none; }\n.callposts_2col .post h3 { margin-bottom: 0; }\n.callposts_2col .clr { height: 20px; }\n.callposts.whitespace { background: #fff; width: 225px; float: right; margin-right: -10px; padding: 0 0 10px 10px; }\n.callposts .floatbox { background-color: #eee; padding: 15px; float: right; margin: 10px -10px 10px 10px;}\n.clr { clear: both; }");

    add_option('ahscp_tmplnum', '3');
    add_option('ahscp_tmpl_1_title', 'Simple linked titles list');
    add_option('ahscp_tmpl_1_group', '1');
    add_option('ahscp_tmpl_1_text', '<li><a href="%%URL%%">%%TITLE%%</a> %%EDITLINK%%</li>');
    add_option('ahscp_tmpl_2_title', 'Just title and full content');
    add_option('ahscp_tmpl_2_text', "<div class=\"post\">
<h3>%%TITLE%% %%EDITLINK%%</h3>
<p>%%CONTENT%%</p>
</div>");
    add_option('ahscp_tmpl_3_title', 'Linked title, image, excerpt and continue link');
    add_option('ahscp_tmpl_3_text', "<div class=\"post %%CATEGORY%%\">
<h3><a href=\"%%URL%%\">%%TITLE%%</a> %%EDITLINK%%</h3>
%%IMAGE%%<p>%%EXCERPT%%...<a href=\"%%URL%%\">More...</a></p>
</div>");
}

register_activation_hook( plugin_dir_path(__FILE__).'ahs_callposts.php', 'ahs_callposts_activate');
add_action('admin_menu', 'ahscp_add_admin');

?>