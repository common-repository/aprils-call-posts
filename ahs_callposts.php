<?php
/*
Plugin Name: April's Call Posts
Plugin URI: http://springthistle.com/wordpress/plugin_callposts
Description: Via shortcode, lets you call in a list of posts that are filtered, displayed and ordered based on criteria you provide. <a href="options-general.php?page=ahs_callposts_admin.php">Edit Settings</a>.
Version: 2.1.1
Author: Aaron Hodge Silver
Author URI: http://springthistle.com/
License: GPL2

    Copyright 2011  Aaron Hodge Silver  (email : aaron@springthistle.com)

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

/*
 * ahscp_callposts_handler()
 * gets the post_content & title of the most recent published post in given category
 * to be used via shortcode only [ahscp_callposts]
 * @param string shortcode attributes
 * @returns string with post title and content
 */
$ahscpsett = array();
function ahscp_callposts_handler($atts) {
	$out = '';
	global $post, $ahscpsett;
	extract(shortcode_atts(array(
		'type' => null,
		'category' => null,
		'category_tax' => null,
		'custom_field'=>null,
		'title'=>null,
		'numberposts'=>1,
		'class'=>'post',
		'sidebox'=>null,
		'sideboxsize'=>'small',
		'content_style'=>null,
		'separator'=>', ',
		'cols'=>1,
		'col_item_height'=>260,
		'col_item_width'=>320,
		'showthumb'=>false,
		'thumbsize'=>'medium',
		'order'=>'DESC',
		'dateformat'=>null,
		'orderby'=>'post_date',
		'continue_text'=>'Read more &raquo;',
		'pre_continue_text'=>'',
		'linktitle'=>'true',
		'template'=>0,
		'post_type'=>'post',
		'heading'=>null,
		'headingtag'=>'h3',
		'ids'=>null,
	), $atts));

	// for backwards compatibility
	if (empty($type)) $type = $category;
	if ($separator=='<li>') $separator = 'list';

	// order by default or by custom field?
	// let shortcode override options default
	if ($custom_field===null && $custom_field !== 'false') $custom_field = get_option('ahscp_customfield');
	if (empty($title)) $title = get_option('ahscp_titletype');
	if (empty($orderby)) $orderby = get_option('ahscp_orderby');
	if (empty($content_style)) $content_style = get_option('ahscp_contentstyle');
	if (!$dateformat) $dateformat = get_option('ahscp_dateformat');
	if (empty($title)) $title = 'h3';
	if (preg_match('/^[0-9|,]*$/',$thumbsize)) {
		$thumbsize = preg_split('/,/',$thumbsize);
	}
	if (empty($ids)) $specific_ids = '';
	else $specific_ids = '&include='.$ids;
	// globalize options
	$ahscpsett['continue_text'] = $continue_text;
	$ahscpsett['pre_continue_text'] = $pre_continue_text;
	$ahscpsett['dateformat'] = $dateformat;
	$ahscpsett['title'] = $title;
	$ahscpsett['class'] = $class;
	$ahscpsett['content_style'] = $content_style;
	$ahscpsett['showthumb'] = $showthumb;
	$ahscpsett['thumbsize'] = $thumbsize;
	$ahscpsett['separator'] = $separator;
	$ahscpsett['linktitle'] = $linktitle;
	$ahscpsett['col_item_height'] = $col_item_height;
	$ahscpsett['template'] = $template;

	// for other taxonomies
	if ($category_tax) {
		$category_label=$category_tax;
	} else {
		$category_label='category';
	}

	if ($category=='byid') {
		$post_ids = explode(',',$ids);
		$args = array(
			'numberposts'	=> -1,
			'orderby'		=> $orderby,
			'order'			=> $order,
			'post_type'		=> $post_type,
			'post__in'		=> $post_ids,
		);
		$posts = get_posts($args);
		$out = ahscp_spit_posts($posts);
	} else {
		// if $category/$type has commas, turn it into a list of IDs
		$catids = ahscp_get_cats_array($category_label);
		$category_ids = "";
		if (!empty($type)) {
			if (preg_match('/,/',$type)) {
				$cnames = preg_split("/,[ ]*/",$type);
				$ahscpsett['single_cat_name'] = $cnames[0];
				foreach ($cnames as $n) $category_ids .= $catids[$n].',';
			} else {
				if ($category_label == 'category') $category_ids = $catids[$type];
				else $category_ids = $type;
				$ahscpsett['single_cat_name'] = $type;
			}
		}

		// get two lists of posts. the first ordered by the custom field, the second those without the custom field
		if (!empty($custom_field)) $posts1 = get_posts('numberposts='.$numberposts.'&'.$category_label.'='.$category_ids.'&meta_key='.$custom_field.'&orderby=meta_value&order='.$order.'&post_type='.$post_type.$specific_ids);
		$posts2 = get_posts('numberposts='.$numberposts.'&'.$category_label.'='.$category_ids.'&orderby='.$orderby.'&order='.$order.'&post_type='.$post_type.$specific_ids);
		if (!isset($posts1)) $posts1 = array();
		$the_posts = create_posts_list($posts1, $posts2, $numberposts);
		$out = ahscp_spit_posts($the_posts);
	}
	// was anything even returned?
	if (strlen($out) > 0) {
		if (!$ahscpsett['template']) {
			if ($ahscpsett['content_style']=='title' && strstr('list',$ahscpsett['separator'])) {
				$out = '<ul>'.$out.'</ul>';
			}

			if ($sidebox!=null) {
				$sizes = array('small'=>'210','medium'=>'300','large'=>'350','xlarge'=>'425');
				$out = '<div class="callposts whitespace" style="width: '.$sizes[$sideboxsize].'px;"><div class="floatbox">'.$out.'</div></div>';
			}

			if ($cols > 1) {
				$style = '<style>.callposts_2col .post { height: '.$ahscpsett['col_item_height'].'px; width: '.$col_item_width.'px; } ';
				if ($ahscpsett['showthumb']==true) $style .= '.wp-caption-text { display: none; }';
				$style .= '</style>';
				$out = $style.'<div class="callposts_2col">'.$out.'<div class="clr">&nbsp;</div></div>';
			}
		} else {
			if (get_option('ahscp_tmpl_'.$ahscpsett['template'].'_group')==1)
				$out = '<ul>'.$out.'</ul>';
		}

		$finalstring = '<div class="ahs_callposts_div';
		if ($ahscpsett['template']) $finalstring .= ' template-'.$ahscpsett['template'];
		$finalstring .= '">';
		if ($heading) $finalstring .= '<'.$headingtag.'>'.$heading.'</'.$headingtag.'>';
		$finalstring .= $out;
		$finalstring .= '</div>';

		return $finalstring;
	}

}
/*
 * creates a string with post title and content, in a div
 * used by ahscp_callposts_handler
 * @param	array	of wordpress posts
 * @returns	string	with html formatted stuff to echo
 */
function ahscp_spit_posts($posts) {
	$out = '';
	global $more, $ahscpsett;
	$urlpath = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	foreach($posts as $post) :
		setup_postdata($post);
		$more = 0;
		// Using a template?
		if ($ahscpsett['template']) {
			$tmpl = stripslashes(get_option('ahscp_tmpl_'.$ahscpsett['template'].'_text'));

			if ($url = get_edit_post_link($post->ID)) $editlink = ' <a href="'.$url.'"><img src="'.$urlpath.'icon-edit.gif" width="14" alt="Edit" title="Edit this post"></a>';

			$tmpl = str_replace('%%TITLE%%',$post->post_title,$tmpl);
			$tmpl = str_replace('%%URL%%',get_permalink($post->ID),$tmpl);
			$tmpl = str_replace('%%IMAGE%%',get_the_post_thumbnail($post->ID, $ahscpsett['thumbsize']),$tmpl);
			$tmpl = str_replace('%%DATE%%',get_the_time($ahscpsett['dateformat'],$post->ID),$tmpl);

			if (strpos($tmpl, '%%CONTENT') !== FALSE) {
				$content = get_the_content($ahscpsett['continue_text']);
				$content = apply_filters('the_content', $content);
				$content = str_replace(']]>', ']]&gt;', $content);
				$content = preg_replace('/<a href="[^"]+" class="more\-link">Read more &raquo;<\/a>/','<a href="'.get_permalink($post->ID).'" class="more-link">Read more &raquo;</a>',$content);
				$tmpl = str_replace('%%CONTENT%%',$content,$tmpl);
				$tmpl = str_replace('%%CONTENTNOIMG%%',strip_tags($content,'<b><strong><p><br><a><i><em><h3><h4><u><ul><ol><li><table><tr><td><th><div><span><script><iframe>'),$tmpl);
			}

			if (strpos($tmpl, '%%EXCERPT') !== FALSE) {
				if (!empty($post->post_excerpt)) $excerpt = $post->post_excerpt;
				else $excerpt = get_the_excerpt($post);
				$tmpl = str_replace('%%EXCERPT%%',$excerpt,$tmpl);
			}
			$tmpl = str_replace('%%EDITLINK%%',$editlink,$tmpl);
			$tmpl = str_replace('%%CATEGORY%%',$ahscpsett['single_cat_name'],$tmpl);
			$out .= $tmpl;
		}
		// Not using a template
		else if ($ahscpsett['content_style'] != 'title') {
			$out .= '<div class="'.$ahscpsett['class'].'">';
			$out .= '<'.$ahscpsett['title'].'>';
			if (!empty($ahscpsett['dateformat'])) $out .= get_the_time($ahscpsett['dateformat'],$post->ID).' - ';
			// post title
			if ($ahscpsett['linktitle']!='false') $out .= '<a href="'.get_permalink($post->ID).'">';
			$out .= $post->post_title;
			if ($ahscpsett['linktitle']!='false') $out .= '</a>';
			// edit link
			if ($url = get_edit_post_link($post->ID)) $out .= ' <a href="'.$url.'"><img src="'.$urlpath.'icon-edit.gif" width="14" alt="Edit" title="Edit this post"></a>';
			$out .= '</'.$ahscpsett['title'].'>';
			if ($ahscpsett['showthumb']==true) {
				$out .= get_the_post_thumbnail($post->ID, $ahscpsett['thumbsize']);
			}
			if ($ahscpsett['content_style']=='excerpt') {
				if (!empty($post->post_excerpt)) $excerpt = $post->post_excerpt;
//					else $excerpt = get_the_excerpt();
				else $excerpt = get_the_excerpt();
				$out .= '<p>'.$excerpt.' '.$ahscpsett['pre_continue_text'].'<a href="'.get_permalink($post->ID).'">'.$ahscpsett['continue_text'].'</a></p>';
			} else {
				// default content_style=full
				$content = get_the_content($ahscpsett['continue_text']);
				$content = apply_filters('the_content', $content);
				$content = str_replace(']]>', ']]&gt;', $content);
				$content = preg_replace('/<a href="[^"]+" class="more\-link">Read more &raquo;<\/a>/','<a href="'.get_permalink($post->ID).'" class="more-link">Read more &raquo;</a>',$content);
				if ($ahscpsett['showthumb']==true) $content = strip_tags($content,'<b><a><strong><p><br><br />');
				$out .= $content;
			}
			$out .= '</div>'; // end <div class="post"> (or whatever)
		} else {
			if (strstr('list',$ahscpsett['separator'])) {
				$out .= '<li>'.'<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>'.'</li>'."\n";
			} else {
				$out .= '<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>'.$ahscpsett['separator'];
			}
		}
	endforeach;
	return $out;
}

add_shortcode('ahs_callposts', 'ahscp_callposts_handler');

/*
 * ahscp_get_cats_array()
 * gets an array of categories ID=>NAME
 * @returns the array of categories
 */
function ahscp_get_cats_array($category_label) {
	global $wpdb;

	$sql = "SELECT tt.term_id, t.slug  FROM ".$wpdb->prefix."term_taxonomy tt, ".$wpdb->prefix."terms t WHERE tt.taxonomy LIKE '".$category_label."' AND tt.term_id=t.term_id ORDER BY t.slug";
	$result = $wpdb->get_results($sql);

	$catids=array();
	foreach ($result as $i) {
		$catids[$i->slug]=$i->term_id;
	}

	return $catids;
}

/**
* Creates the final array of posts we actually want to use
*
* @param	array	The first list of posts, ordered by custom field
* @param	array	The second list of posts, ordered not by custom field
* @returns array
**/
function create_posts_list($posts1, $posts2, $numberposts) {
	if (!empty($posts1)) {
		$the_posts = array(); // to return
		// discard any items in $posts2 that have a matching id in $posts1
		$post1_ids = $post2_ids = array();
		foreach ($posts1 as $p1) {
			$post1_ids[] = $p1->ID;
			$the_posts[] = $p1;
		}
		foreach ($posts2 as $p2) {
			if (!in_array($p2->ID, $post1_ids)) {
				$the_posts[] = $p2;
			}
		}
		// make sure we haven't got more than the limit
		return array_slice($the_posts, 0, $numberposts);
	} else {
		return $posts2;
	}
}

// this functions adds the CSS to the head
function ahscp_callposts_styles() {
	echo "\n<!-- Begin css from April\'s Call Posts -->\n";
	echo '<style type="text/css">'."\n";
	echo htmlspecialchars(get_option('ahscp_css'));
	echo "\n</style>";
	echo "\n<!-- End css from April\'s Call Posts -->\n\n";
}

add_action('wp_head', 'ahscp_callposts_styles');

require('ahs_callposts_admin.php');

?>