<?php
/**
 *  @generated
 */
class Thrift_RemindingSettingsQuery {
  static $_TSPEC;

  public $frequencyType = null;
  public $pager = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'frequencyType',
          'type' => TType::I16,
          ),
        2 => array(
          'var' => 'pager',
          'type' => TType::STRUCT,
          'class' => 'Thrift_Pager',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['frequencyType'])) {
        $this->frequencyType = $vals['frequencyType'];
      }
      if (isset($vals['pager'])) {
        $this->pager = $vals['pager'];
      }
    }
  }

  public function getName() {
    return 'Thrift_RemindingSettingsQuery';
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
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->frequencyType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRUCT) {
            $this->pager = new Thrift_Pager();
            $xfer += $this->pager->read($input);
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
    $xfer += $output->writeStructBegin('Thrift_RemindingSettingsQuery');
    if ($this->frequencyType !== null) {
      $xfer += $output->writeFieldBegin('frequencyType', TType::I16, 1);
      $xfer += $output->writeI16($this->frequencyType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->pager !== null) {
      if (!is_object($this->pager)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('pager', TType::STRUCT, 2);
      $xfer += $this->pager->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
