<?php
/**
 *  @generated
 */
class Monipla_getMinusPoint_args {
  static $_TSPEC;

  public $pointBookQuery = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'pointBookQuery',
          'type' => TType::STRUCT,
          'class' => 'Thrift_PointBookQuery',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['pointBookQuery'])) {
        $this->pointBookQuery = $vals['pointBookQuery'];
      }
    }
  }

  public function getName() {
    return 'Monipla_getMinusPoint_args';
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
            $this->pointBookQuery = new Thrift_PointBookQuery();
            $xfer += $this->pointBookQuery->read($input);
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
    $xfer += $output->writeStructBegin('Monipla_getMinusPoint_args');
    if ($this->pointBookQuery !== null) {
      if (!is_object($this->pointBookQuery)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('pointBookQuery', TType::STRUCT, 1);
      $xfer += $this->pointBookQuery->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
