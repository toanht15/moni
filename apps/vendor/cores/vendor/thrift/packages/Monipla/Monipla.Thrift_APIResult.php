<?php
/**
 *  @generated
 */
class Thrift_APIResult {
  static $_TSPEC;

  public $status = null;
  public $errors = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'status',
          'type' => TType::I32,
          ),
        2 => array(
          'var' => 'errors',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_APIError',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['status'])) {
        $this->status = $vals['status'];
      }
      if (isset($vals['errors'])) {
        $this->errors = $vals['errors'];
      }
    }
  }

  public function getName() {
    return 'Thrift_APIResult';
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
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->status);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::LST) {
            $this->errors = array();
            $_size0 = 0;
            $_etype3 = 0;
            $xfer += $input->readListBegin($_etype3, $_size0);
            for ($_i4 = 0; $_i4 < $_size0; ++$_i4)
            {
              $elem5 = null;
              $elem5 = new Thrift_APIError();
              $xfer += $elem5->read($input);
              $this->errors []= $elem5;
            }
            $xfer += $input->readListEnd();
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
    $xfer += $output->writeStructBegin('Thrift_APIResult');
    if ($this->status !== null) {
      $xfer += $output->writeFieldBegin('status', TType::I32, 1);
      $xfer += $output->writeI32($this->status);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->errors !== null) {
      if (!is_array($this->errors)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('errors', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRUCT, count($this->errors));
        {
          foreach ($this->errors as $iter6)
          {
            $xfer += $iter6->write($output);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
