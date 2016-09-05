<?php
/**
 *  @generated
 */
class Monipla_getOptin_args {
  static $_TSPEC;

  public $query = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'query',
          'type' => TType::STRUCT,
          'class' => 'Thrift_UserQuery',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['query'])) {
        $this->query = $vals['query'];
      }
    }
  }

  public function getName() {
    return 'Monipla_getOptin_args';
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
            $this->query = new Thrift_UserQuery();
            $xfer += $this->query->read($input);
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
    $xfer += $output->writeStructBegin('Monipla_getOptin_args');
    if ($this->query !== null) {
      if (!is_object($this->query)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('query', TType::STRUCT, 1);
      $xfer += $this->query->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
