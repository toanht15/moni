<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwParserBase' );
class RSSParser extends aafwParserBase {
  public function getContentType(){
    return 'application/rss+xml';
  }

	public function in ( $data ) {
		if    ( is_file ( $data ) ) return simplexml_load_file   ( $data );
		else                        return simplexml_load_string ( $data );
	}

	public function out ( $data ) { ob_start() ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	xmlns:xhtml="http://www.w3.org/1999/xhtml"
	<?php if ( $data['xmlns'] ):?>
	<?php foreach ( $data['xmlns'] as $key => $url ):?>
	xmlns:<?php echo $key?>="<?php echo $url?>"
	<?php endforeach; ?>
	<?php endif ;?>
>
<channel>
	<title><?php asign ( $data['title'] )?> </title>
	<?php if ( $data['self'] ):?>
	<atom:link href="<?php write_html ($data['self']) ?>" rel="self" type="application/rss+xml" />
	<?php endif; ?>
	<link><?php write_html ( $data['link']) ?></link>
	<description><?php asign ( $data['description'] ) ?></description>
	<?php if ( $data['language'] ):?>
	<language><?php asign ( $data['language'] ) ?></language>
	<?php endif; ?>
	<?php foreach ( $data['items'] as $row ): ?>
		<item>
			<title><?php asign( $row['title'] ) ?></title>
			<link><?php write_html ( $row['link']) ?></link>
			<pubDate><?php asign ( $row['pubDate'] )?> </pubDate>
			<dc:creator><?php asign ( $row['dc:creator'])?></dc:creator>
			<category><![CDATA[<?php asign( $row['category'] )?>]]></category>
			<description><![CDATA[<?php write_html( $row['description'] )?>]]></description>
			<?php if ( $row['content:encoded'] ):?>
			<content:encoded><![CDATA[<?php write_html( $row['content:encoded'] )?>]]></content:encoded>
			<?php elseif( $row['description'] ) :?>
			<content:encoded><![CDATA[<?php write_html( $row['description'] )?>]]></content:encoded>
			<?php endif;?>
			<?php if ( $row['enclosure'] ):?>
			<enclosure url="<?php write_html( $row['enclosure']['url'])?>" length="<?php write_html( $row['enclosure']['length'] )?>" type="<?php write_html( $row['enclosure']['type'])?>" />
			<?php endif; ?>
			<?php if ( $row['extension'] ):?>
			<?php foreach ( $row['extension'] as $key => $value ):?>
			<?php if ( !$value ) continue ?>
			<<?php asign($key)?>><?php asign( is_array( $value ) ? join( ',', $value ) : $value )?></<?php asign($key)?>>
			<?php endforeach; ?>
			<?php endif;?>
		</item>
	<?php endforeach; ?>
</channel>
</rss>
<?php return trim( ob_get_clean() ) ;	}
}
