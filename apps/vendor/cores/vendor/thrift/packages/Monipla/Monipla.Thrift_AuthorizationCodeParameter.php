<?php
/**
 *  @generated
 */
class Thrift_AuthorizationCodeParameter {
  static $_TSPEC;

  public $clientId = null;
  public $code = null;
  public $enterpriseId = null;
  public $permissionList = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'clientId',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'code',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'enterpriseId',
          'type' => TType::I64,
          ),
        4 => array(
          'var' => 'permissionList',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_PermissionParameter',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['clientId'])) {
        $this->clientId = $vals['clientId'];
      }
      if (isset($vals['code'])) {
        $this->code = $vals['code'];
      }
      if (isset($vals['enterpriseId'])) {
        $this->enterpriseId = $vals['enterpriseId'];
      }
      if (isset($vals['permissionList'])) {
        $this->permissionList = $vals['permissionList'];
      }
    }
  }

  public function getName() {
    return 'Thrift_AuthorizationCodeParameter';
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
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->clientId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->code);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->enterpriseId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::LST) {
            $this->permissionList = array();
            $_size97 = 0;
            $_etype100 = 0;
            $xfer += $input->readListBegin($_etype100, $_size97);
            for ($_i101 = 0; $_i101 < $_size97; ++$_i101)
            {
              $elem102 = null;
              $elem102 = new Thrift_PermissionParameter();
              $xfer += $elem102->read($input);
              $this->permissionList []= $elem102;
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
    $xfer += $output->writeStructBegin('Thrift_AuthorizationCodeParameter');
    if ($this->clientId !== null) {
      $xfer += $output->writeFieldBegin('clientId', TType::STRING, 1);
      $xfer += $output->writeString($this->clientId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->code !== null) {
      $xfer += $output->writeFieldBegin('code', TType::STRING, 2);
      $xfer += $output->writeString($this->code);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->enterpriseId !== null) {
      $xfer += $output->writeFieldBegin('enterpriseId', TType::I64, 3);
      $xfer += $output->writeI64($this->enterpriseId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->permissionList !== null) {
      if (!is_array($this->permissionList)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('permissionList', TType::LST, 4);
      {
        $output->writeListBegin(TType::STRUCT, count($this->permissionList));
        {
          foreach ($this->permissionList as $iter103)
          {
            $xfer += $iter103->write($output);
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
