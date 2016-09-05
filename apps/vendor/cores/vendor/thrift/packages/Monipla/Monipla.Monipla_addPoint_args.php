<?php
/**
 *  @generated
 */
class Monipla_addPoint_args {
  static $_TSPEC;

  public $point = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'point',
          'type' => TType::STRUCT,
          'class' => 'Thrift_AddPoint',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['point'])) {
        $this->point = $vals['point'];
      }
    }
  }

  public function getName() {
    return 'Monipla_addPoint_args';
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
            $this->point = new Thrift_AddPoint();
            $xfer += $this->point->read($input);
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
    $xfer += $output->writeStructBegin('Monipla_addPoint_args');
    if ($this->point !== null) {
      if (!is_object($this->point)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('point', TType::STRUCT, 1);
      $xfer += $this->point->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
