<?php
class SNSParser {
    const TWITTER_PANEL_TEXT = 1;
    const FACEBOOK_PANEL_TEXT = 2;
    const YOUTUBE_PANEL_TEXT = 3;

    private static $parse_hashtag_function = array(
        self::TWITTER_PANEL_TEXT => 'parseTwitterHashtag',
        self::FACEBOOK_PANEL_TEXT => 'parseFacebookHashtag'
    );

    private function parseTwitterMention($matches) {
        if ($matches[4] == '＠' || $matches[4] == '@') return $matches[0];

        return $matches[1] . '<a href="https://twitter.com/' . $matches[3] . '" target="_blank">' . $matches[2] . $matches[3] . '</a>' . $matches[4];
    }

    private function parseTwitterHashtag($matches) {
        if ($matches[4] == '#' || $matches[4] == '＃' || $matches[4] == '://' || is_numeric($matches[3])) return $matches[0];

        return $matches[1] . '<a href="https://twitter.com/hashtag/' . $matches[3] . '?src=hash" target="_blank">' . $matches[2] . $matches[3] . '</a>' . $matches[4];
    }

    private function parseFacebookHashtag($matches) {
        if (is_numeric($matches[3]) || $matches[4] == '://') return $matches[0];

        return $matches[1] . '<a href="https://www.facebook.com/hashtag/' . $matches[3] . '" target="_blank">' . $matches[2] . $matches[3] . '</a>' . $matches[4];
    }

    private function parseLink($matches) {
        if (preg_match('/[.]{3}$/', $matches[2], $result)) return $matches[0];

        return $matches[1] . '<a href="' . $matches[2] . '" target="_blank">' . $matches[2] . '</a>';
    }

    private function parseUrl( $text ){
        $text = html_entity_decode($text);

        $text = preg_replace_callback(
            '#(^|[^0-9a-zA-Z-\.@/\?&=~\#%+;\,])(https?:\/\/[0-9a-zA-Z-_\.@/\?&=~\#%+;\,]+)#',
            array(self, 'parseLink'),
            $text
        );

        $text = str_replace("\n", "<br/>", $text);
        return $text;
    }

    public static function parseText($text, $type) {
        if (is_array($text) || is_object($text)) return $text;

        $text = self::parseUrl($text);
        
        if ($type != self::TWITTER_PANEL_TEXT && $type != self::FACEBOOK_PANEL_TEXT) return $text;

        // Parsing mention in tweet
        if ($type == self::TWITTER_PANEL_TEXT) {
            $text = preg_replace_callback(
                '/(^|[^0-9A-Za-z_&\/\?]+)([@＠])([A-Za-z_0-9]+)([@＠]*)/iu',
                array(self, 'parseTwitterMention'),
                $text
            );
        }

        // Parsing hashtag in panel text
        $text = preg_replace_callback(
            '/(^|[^0-9A-Za-z_ァ-ヶぁ-んー亜-黑０-９&\/\?]+)([#＃])([A-Za-z_0-9ァ-ヶぁ-んー亜-黑０-９]+)([#＃:\/]*)/iu',
            array(self, self::$parse_hashtag_function[$type]),
            $text
        );

        return $text;
    }
}
