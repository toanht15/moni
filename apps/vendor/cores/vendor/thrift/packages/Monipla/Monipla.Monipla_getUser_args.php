<?php
/**
 *  @generated
 */
class Monipla_getUser_args {
  static $_TSPEC;

  public $accessToken = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'accessToken',
          'type' => TType::STRUCT,
          'class' => 'Thrift_AccessTokenParameter',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['accessToken'])) {
        $this->accessToken = $vals['accessToken'];
      }
    }
  }

  public function getName() {
    return 'Monipla_getUser_args';
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
          if ($ftype == TType::STRUCT) {
            $this->accessToken = new Thrift_AccessTokenParameter();
            $xfer += $this->accessToken->read($input);
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
    $xfer += $output->writeStructBegin('Monipla_getUser_args');
    if ($this->accessToken !== null) {
      if (!is_object($this->accessToken)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('accessToken', TType::STRUCT, 1);
      $xfer += $this->accessToken->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
