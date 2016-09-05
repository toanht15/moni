<?php
/**
 *  @generated
 */
class Thrift_TwitterParameter {
  static $_TSPEC;

  public $consumerKey = null;
  public $consumerSecret = null;
  public $oauthToken = null;
  public $oauthTokenSecret = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'consumerKey',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'consumerSecret',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'oauthToken',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'oauthTokenSecret',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['consumerKey'])) {
        $this->consumerKey = $vals['consumerKey'];
      }
      if (isset($vals['consumerSecret'])) {
        $this->consumerSecret = $vals['consumerSecret'];
      }
      if (isset($vals['oauthToken'])) {
        $this->oauthToken = $vals['oauthToken'];
      }
      if (isset($vals['oauthTokenSecret'])) {
        $this->oauthTokenSecret = $vals['oauthTokenSecret'];
      }
    }
  }

  public function getName() {
    return 'Thrift_TwitterParameter';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->consumerKey);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->consumerSecret);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->oauthToken);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->oauthTokenSecret);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('Thrift_TwitterParameter');
    if ($this->consumerKey !== null) {
      $xfer += $output->writeFieldBegin('consumerKey', TType::STRING, 1);
      $xfer += $output->writeString($this->consumerKey);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->consumerSecret !== null) {
      $xfer += $output->writeFieldBegin('consumerSecret', TType::STRING, 2);
      $xfer += $output->writeString($this->consumerSecret);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->oauthToken !== null) {
      $xfer += $output->writeFieldBegin('oauthToken', TType::STRING, 3);
      $xfer += $output->writeString($this->oauthToken);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->oauthTokenSecret !== null) {
      $xfer += $output->writeFieldBegin('oauthTokenSecret', TType::STRING, 4);
      $xfer += $output->writeString($this->oauthTokenSecret);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
