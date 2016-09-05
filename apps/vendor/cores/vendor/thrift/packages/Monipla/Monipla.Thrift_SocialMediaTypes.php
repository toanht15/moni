<?php
/**
 *  @generated
 */
class Thrift_SocialMediaTypes {
  static $_TSPEC;

  public $result = null;
  public $socialMediaTypes = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'result',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'socialMediaTypes',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_SocialMediaType',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['result'])) {
        $this->result = $vals['result'];
      }
      if (isset($vals['socialMediaTypes'])) {
        $this->socialMediaTypes = $vals['socialMediaTypes'];
      }
    }
  }

  public function getName() {
    return 'Thrift_SocialMediaTypes';
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
          if ($ftype == TType::LST) {
            $this->socialMediaTypes = array();
            $_size220 = 0;
            $_etype223 = 0;
            $xfer += $input->readListBegin($_etype223, $_size220);
            for ($_i224 = 0; $_i224 < $_size220; ++$_i224)
            {
              $elem225 = null;
              $elem225 = new Thrift_SocialMediaType();
              $xfer += $elem225->read($input);
              $this->socialMediaTypes []= $elem225;
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
    $xfer += $output->writeStructBegin('Thrift_SocialMediaTypes');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->socialMediaTypes !== null) {
      if (!is_array($this->socialMediaTypes)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('socialMediaTypes', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRUCT, count($this->socialMediaTypes));
        {
          foreach ($this->socialMediaTypes as $iter226)
          {
            $xfer += $iter226->write($output);
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
