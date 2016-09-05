<?php
/**
 *  @generated
 */
class Thrift_EnterpriseBackDoorLoginParameter {
  static $_TSPEC;

  public $enterpriseId = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'enterpriseId',
          'type' => TType::I64,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['enterpriseId'])) {
        $this->enterpriseId = $vals['enterpriseId'];
      }
    }
  }

  public function getName() {
    return 'Thrift_EnterpriseBackDoorLoginParameter';
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
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->enterpriseId);
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
    $xfer += $output->writeStructBegin('Thrift_EnterpriseBackDoorLoginParameter');
    if ($this->enterpriseId !== null) {
      $xfer += $output->writeFieldBegin('enterpriseId', TType::I64, 1);
      $xfer += $output->writeI64($this->enterpriseId);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
