<?php
require_once('OAuth/OAuth.php');
class aafwOpenSocialAPI {
  private $Key    = null;
  private $Secret = null;
  private $OwnerID = null;
  private $EntryPoint     = null;
  public function __construct( $key, $secret, $owner_id, $entry_point = 'http://api.mixi-platform.com/os/0.8' ){
    $this->Key = $key;
    $this->Secret = $secret;
    $this->OwnerID = $owner_id;
    $this->EntryPoint = $entry_point;
  }

  public function getProfile(){
    return $this->get( $this->EntryPoint . '/' . 'people/@me/@self?xoauth_requestor_id=' . $this->OwnerID );
  }
  
  public function getFriends( $filter = 'hasApp' ){
//    return  $this->get( $this->EntryPoint . '/' . 'people/@me/@friends?xoauth_requestor_id=' . $this->OwnerID . '&filterBy=hasApp' );
    return  $this->get( $this->EntryPoint . '/' . 'people/@me/@friends?xoauth_requestor_id=' . $this->OwnerID  );
  }
  
  private function get( $url ){
    $curl = curl_init( $url );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $curl, CURLOPT_FAILONERROR, false );
    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $curl, CURLOPT_ENCODING, 'gzip' );
    $auth_header = $this->getAuthHeader( $url );
    if ( $auth_header ) curl_setopt( $curl, CURLOPT_HTTPHEADER, array( $auth_header ) );
    $response = curl_exec( $curl );
    if (!$response)  $response = curl_error( $curl );
    curl_close( $curl );
    return json_decode( $response );
  }
  private function getAuthHeader( $url ){
    $consumer = new OAuthConsumer(
      $this->Key,
      $this->Secret
      );
    list( $url, $query ) = explode( '?', $url );
    $params = parse_str( $query );
    if( !$params['xoauth_requestor_id'] ) $params['xoauth_requestor_id'] = $this->OwnerID;
    $request = OAuthRequest::from_consumer_and_token(
      $consumer,
      NULL,
      'GET',
      $url,
      $params
      );
    $request->sign_request( new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL );
    return  $request->to_header( 'api.mixi-platform.com' );
  }
  
  public static function check( $sig, $method = 'MixiSignatureMethod' ){
    $signature_method = new $method;
    $request = OAuthRequest::from_request(
      null,
      null,
      array_merge( $_GET, $_POST )
      );
    return $signature_method->check_signature(
      $request,
      null,
      null,
      $sig
      );
  }
}

class MixiSignatureMethod extends OAuthSignatureMethod_RSA_SHA1 {
  protected function fetch_public_cert(&$request) {
    return <<< EOD
-----BEGIN CERTIFICATE-----
MIICdzCCAeCgAwIBAgIJAOi/chE0MhufMA0GCSqGSIb3DQEBBQUAMDIxCzAJBgNV
BAYTAkpQMREwDwYDVQQKEwhtaXhpIEluYzEQMA4GA1UEAxMHbWl4aS5qcDAeFw0w
OTA0MjgwNzAyMTVaFw0xMDA0MjgwNzAyMTVaMDIxCzAJBgNVBAYTAkpQMREwDwYD
VQQKEwhtaXhpIEluYzEQMA4GA1UEAxMHbWl4aS5qcDCBnzANBgkqhkiG9w0BAQEF
AAOBjQAwgYkCgYEAwEj53VlQcv1WHvfWlTP+T1lXUg91W+bgJSuHAD89PdVf9Ujn
i92EkbjqaLDzA43+U5ULlK/05jROnGwFBVdISxULgevSpiTfgbfCcKbRW7hXrTSm
jFREp7YOvflT3rr7qqNvjm+3XE157zcU33SXMIGvX1uQH/Y4fNpEE1pmX+UCAwEA
AaOBlDCBkTAdBgNVHQ4EFgQUn2ewbtnBTjv6CpeT37jrBNF/h6gwYgYDVR0jBFsw
WYAUn2ewbtnBTjv6CpeT37jrBNF/h6ihNqQ0MDIxCzAJBgNVBAYTAkpQMREwDwYD
VQQKEwhtaXhpIEluYzEQMA4GA1UEAxMHbWl4aS5qcIIJAOi/chE0MhufMAwGA1Ud
EwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADgYEAR7v8eaCaiB5xFVf9k9jOYPjCSQIJ
58nLY869OeNXWWIQ17Tkprcf8ipxsoHj0Z7hJl/nVkSWgGj/bJLTVT9DrcEd6gLa
H5TbGftATZCAJ8QJa3X2omCdB29qqyjz4F6QyTi930qekawPBLlWXuiP3oRNbiow
nOLWEi16qH9WuBs=
-----END CERTIFICATE-----
EOD;
  }
  
  protected function fetch_private_cert(&$request) {
  return <<< EOD
-----BEGIN CERTIFICATE-----
MIICdzCCAeCgAwIBAgIJAOi/chE0MhufMA0GCSqGSIb3DQEBBQUAMDIxCzAJBgNV
BAYTAkpQMREwDwYDVQQKEwhtaXhpIEluYzEQMA4GA1UEAxMHbWl4aS5qcDAeFw0w
OTA0MjgwNzAyMTVaFw0xMDA0MjgwNzAyMTVaMDIxCzAJBgNVBAYTAkpQMREwDwYD
VQQKEwhtaXhpIEluYzEQMA4GA1UEAxMHbWl4aS5qcDCBnzANBgkqhkiG9w0BAQEF
AAOBjQAwgYkCgYEAwEj53VlQcv1WHvfWlTP+T1lXUg91W+bgJSuHAD89PdVf9Ujn
i92EkbjqaLDzA43+U5ULlK/05jROnGwFBVdISxULgevSpiTfgbfCcKbRW7hXrTSm
jFREp7YOvflT3rr7qqNvjm+3XE157zcU33SXMIGvX1uQH/Y4fNpEE1pmX+UCAwEA
AaOBlDCBkTAdBgNVHQ4EFgQUn2ewbtnBTjv6CpeT37jrBNF/h6gwYgYDVR0jBFsw
WYAUn2ewbtnBTjv6CpeT37jrBNF/h6ihNqQ0MDIxCzAJBgNVBAYTAkpQMREwDwYD
VQQKEwhtaXhpIEluYzEQMA4GA1UEAxMHbWl4aS5qcIIJAOi/chE0MhufMAwGA1Ud
EwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADgYEAR7v8eaCaiB5xFVf9k9jOYPjCSQIJ
58nLY869OeNXWWIQ17Tkprcf8ipxsoHj0Z7hJl/nVkSWgGj/bJLTVT9DrcEd6gLa
H5TbGftATZCAJ8QJa3X2omCdB29qqyjz4F6QyTi930qekawPBLlWXuiP3oRNbiow
nOLWEi16qH9WuBs=
-----END CERTIFICATE-----
EOD;
  }
}
