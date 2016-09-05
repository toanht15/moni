<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of summariseEnd
 *
 * @author ishida
 */
class summariseEnd {
	function doMethod( $args ){
		list ( $no_cache_mode ) = $args;
		$lines = ob_get_clean();
		$no_cache_mode = true;
		if ( !$no_cache_mode  ) {
			$css_fn = '/tmp/' . str_replace ( '/', '-', $_SERVER['REQUEST_URI'] ) . '-all_css';
			$js_fn  = '/tmp/' . str_replace ( '/', '-', $_SERVER['REQUEST_URI'] ) . '-all_js';
			if ( !is_file ( $css_fn ) || time() - strtotime ( filemtime( $css_fn ) ) > 3600 || 
				   !is_file ( $js_fn )  || time() - strtotime ( filemtime( $js_fn ) ) > 3600  ){
				list( $css_content, $js_content, $except ) = $this->getContent( $lines );
				file_put_contents( $css_fn , $css_content );
			  file_put_contents( $js_fn  , $js_content );
			}
			print '<link rel="stylesheet" href="/statics/' . preg_replace ( '#^/tmp/#', '',  $css_fn ) . '.css" type="text/css" />';
			print "\n$except";
			print '<script type="text/javascript" charset="UTF-8" src="/statics/' .preg_replace ( '#^/tmp/#', '', $js_fn  ). '.js"></script>';
		} else {
			list( $css_content, $js_content, $except ) = $this->getContent( $lines );
			if ( $css_content ) print '<style type="text/css">'. preg_replace ( '#url\(\.\./#','url(/', $css_content ) .'</style>';
			print $except;
			if ( $js_content ) print '<script type="text/javascript" charset="UTF-8">' . $js_content .'</script>';
		}
	}
	private function getContent ( $lines ){
		$js_content  = '';
		$css_content = '';
		$except = '';
		foreach ( preg_split ( '#\n#', $lines ) as $row ){
			if ( preg_match ( '#^<!--#', $row ) ) {
				$except  .= $row . "\n";
				continue;
			}
			if ( preg_match ( '#link.+stylesheet#i', $row ) ) {
				if ( preg_match ( '#href="(.+?)"#', $row, $tmp ) ){
					if ( is_file (  DOC_ROOT . $tmp[1] ) ){
						$css_content .= file_get_contents ( DOC_ROOT . $tmp[1] );
					}
				}
			}
			elseif( preg_match ( '#<script#', $row ) ){
				if ( preg_match ( '#src="(.+?)"#', $row, $tmp ) ){
					if ( is_file (  DOC_ROOT . $tmp[1] ) ){
						$js_content .= file_get_contents ( DOC_ROOT . $tmp[1] ) ;
					}
				}
			}
		}
		return array ( $css_content, $js_content, $except );
	}
}
