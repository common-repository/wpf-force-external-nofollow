<?php
/*
Plugin Name: WPF Force External Nofollow
Plugin URI: http://blog.wordpressforge.com/plugins/force-external-nofollow.html
Description: Simple, `rel="nofollow"` will be added automatically for all the external links on posts or pages, removing `dofollow` if it exists.
Version: 1.3
Author: Mike Johnson (AfterDarkMike)
Author URI: 
License: GPL2
*/

add_filter( 'the_content', 'wpf_force_nofollow' );

function wpf_force_nofollow( $content )
{
	$regex = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
	if( preg_match_all( "/$regex/siU", $content, $matches, PREG_SET_ORDER ) )
	{
		if( ! empty( $matches ) )
		{
			$site_url = get_option( 'siteurl' );
			for( $i=0; $i < count( $matches ); $i++ )
			{
				$tag = $tag2 = $url = $matches[$i][0];

				$pattern = '/rel\s*=\s*"\s*(.*)\s*"/';
				preg_match( $pattern, $tag2, $match, PREG_OFFSET_CAPTURE );

				$rel = array();
				if( isset( $match[0], $match[1] ) ) 
				{
					if( is_array( $match[1] ) ) $match[1] = $match[1][0];
					$tag = str_replace( $match[0], '', $tag2 );
					
					foreach( explode( ' ', $match[1] ) as $r )
					{
						if( $r == 'dofollow' || $r == 'nofollow' ) continue;
						$rel[] = $r;
					}
				}
				$rel[] = 'nofollow';
				$rel = implode( ' ', $rel );
				$tag = str_replace( '<a ', '<a rel="' . $rel . '" ', $tag );
				
				preg_match('/href=["\']?([^"\'>]+)["\']?/', $url, $m);
				if( isset( $m[1] ) && strpos( $m[1], 'http' ) === 0 && strpos( $m[1], $site_url ) === FALSE  )
				{
					$content = str_replace( $tag2, $tag, $content );
				}
			}
		}
	}
	
	$content = str_replace( ']]>', ']]&gt;', $content );
	return $content;
}
