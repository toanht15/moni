<?php
/**
 *  @generated
 */
class Thrift_Application {
  static $_TSPEC;

  public $result = null;
  public $id = null;
  public $clientId = null;
  public $name = null;
  public $redirectUri = null;
  public $urlScheme = null;
  public $withdrawUrl = null;
  public $canFullControl = null;
  public $permissionType = null;
  public $iconPath = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'result',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'id',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'clientId',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'name',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'redirectUri',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'urlScheme',
          'type' => TType::STRING,
          ),
        7 => array(
          'var' => 'withdrawUrl',
          'type' => TType::STRING,
          ),
        8 => array(
          'var' => 'canFullControl',
          'type' => TType::I16,
          ),
        9 => array(
          'var' => 'permissionType',
          'type' => TType::I16,
          ),
        10 => array(
          'var' => 'iconPath',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['result'])) {
        $this->result = $vals['result'];
      }
      if (isset($vals['id'])) {
        $this->id = $vals['id'];
      }
      if (isset($vals['clientId'])) {
        $this->clientId = $vals['clientId'];
      }
      if (isset($vals['name'])) {
        $this->name = $vals['name'];
      }
      if (isset($vals['redirectUri'])) {
        $this->redirectUri = $vals['redirectUri'];
      }
      if (isset($vals['urlScheme'])) {
        $this->urlScheme = $vals['urlScheme'];
      }
      if (isset($vals['withdrawUrl'])) {
        $this->withdrawUrl = $vals['withdrawUrl'];
      }
      if (isset($vals['canFullControl'])) {
        $this->canFullControl = $vals['canFullControl'];
      }
      if (isset($vals['permissionType'])) {
        $this->permissionType = $vals['permissionType'];
      }
      if (isset($vals['iconPath'])) {
        $this->iconPath = $vals['iconPath'];
      }
    }
  }

  public function getName() {
    return 'Thrift_Application';
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
            $this->result = new Thrift_APIResult();
            $xfer += $this->result->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->id);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->clientId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->name);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->redirectUri);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->urlScheme);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->withdrawUrl);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->canFullControl);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 9:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->permissionType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 10:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->iconPath);
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
    $xfer += $output->writeStructBegin('Thrift_Application');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->id !== null) {
      $xfer += $output->writeFieldBegin('id', TType::I64, 2);
      $xfer += $output->writeI64($this->id);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->clientId !== null) {
      $xfer += $output->writeFieldBegin('clientId', TType::STRING, 3);
      $xfer += $output->writeString($this->clientId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->name !== null) {
      $xfer += $output->writeFieldBegin('name', TType::STRING, 4);
      $xfer += $output->writeString($this->name);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->redirectUri !== null) {
      $xfer += $output->writeFieldBegin('redirectUri', TType::STRING, 5);
      $xfer += $output->writeString($this->redirectUri);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->urlScheme !== null) {
      $xfer += $output->writeFieldBegin('urlScheme', TType::STRING, 6);
      $xfer += $output->writeString($this->urlScheme);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->withdrawUrl !== null) {
      $xfer += $output->writeFieldBegin('withdrawUrl', TType::STRING, 7);
      $xfer += $output->writeString($this->withdrawUrl);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->canFullControl !== null) {
      $xfer += $output->writeFieldBegin('canFullControl', TType::I16, 8);
      $xfer += $output->writeI16($this->canFullControl);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->permissionType !== null) {
      $xfer += $output->writeFieldBegin('permissionType', TType::I16, 9);
      $xfer += $output->writeI16($this->permissionType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->iconPath !== null) {
      $xfer += $output->writeFieldBegin('iconPath', TType::STRING, 10);
      $xfer += $output->writeString($this->iconPath);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
