<?php
/**
 *  @generated
 */
class Thrift_Permissions {
  static $_TSPEC;

  public $status = null;
  public $permissions = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'status',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'permissions',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_Permission',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['status'])) {
        $this->status = $vals['status'];
      }
      if (isset($vals['permissions'])) {
        $this->permissions = $vals['permissions'];
      }
    }
  }

  public function getName() {
    return 'Thrift_Permissions';
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
            $this->permissions = array();
            $_size241 = 0;
            $_etype244 = 0;
            $xfer += $input->readListBegin($_etype244, $_size241);
            for ($_i245 = 0; $_i245 < $_size241; ++$_i245)
            {
              $elem246 = null;
              $elem246 = new Thrift_Permission();
              $xfer += $elem246->read($input);
              $this->permissions []= $elem246;
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
    $xfer += $output->writeStructBegin('Thrift_Permissions');
    if ($this->status !== null) {
      if (!is_object($this->status)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('status', TType::STRUCT, 1);
      $xfer += $this->status->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->permissions !== null) {
      if (!is_array($this->permissions)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('permissions', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRUCT, count($this->permissions));
        {
          foreach ($this->permissions as $iter247)
          {
            $xfer += $iter247->write($output);
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
