<?php
/**
 *  @generated
 */
class Monipla_changeMailAddressByUser_args {
  static $_TSPEC;

  public $userQuery = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'userQuery',
          'type' => TType::STRUCT,
          'class' => 'Thrift_UserQuery',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['userQuery'])) {
        $this->userQuery = $vals['userQuery'];
      }
    }
  }

  public function getName() {
    return 'Monipla_changeMailAddressByUser_args';
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
            $this->userQuery = new Thrift_UserQuery();
            $xfer += $this->userQuery->read($input);
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
    $xfer += $output->writeStructBegin('Monipla_changeMailAddressByUser_args');
    if ($this->userQuery !== null) {
      if (!is_object($this->userQuery)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('userQuery', TType::STRUCT, 1);
      $xfer += $this->userQuery->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
