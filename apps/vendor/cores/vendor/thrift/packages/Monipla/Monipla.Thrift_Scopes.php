<?php
/**
 *  @generated
 */
class Thrift_Scopes {
  static $_TSPEC;

  public $status = null;
  public $scopes = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'status',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'scopes',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_Scope',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['status'])) {
        $this->status = $vals['status'];
      }
      if (isset($vals['scopes'])) {
        $this->scopes = $vals['scopes'];
      }
    }
  }

  public function getName() {
    return 'Thrift_Scopes';
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
            $this->status = new Thrift_APIResult();
            $xfer += $this->status->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::LST) {
            $this->scopes = array();
            $_size234 = 0;
            $_etype237 = 0;
            $xfer += $input->readListBegin($_etype237, $_size234);
            for ($_i238 = 0; $_i238 < $_size234; ++$_i238)
            {
              $elem239 = null;
              $elem239 = new Thrift_Scope();
              $xfer += $elem239->read($input);
              $this->scopes []= $elem239;
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
    $xfer += $output->writeStructBegin('Thrift_Scopes');
    if ($this->status !== null) {
      if (!is_object($this->status)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('status', TType::STRUCT, 1);
      $xfer += $this->status->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->scopes !== null) {
      if (!is_array($this->scopes)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('scopes', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRUCT, count($this->scopes));
        {
          foreach ($this->scopes as $iter240)
          {
            $xfer += $iter240->write($output);
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
