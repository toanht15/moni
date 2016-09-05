<?php
/**
 *  @generated
 */
class Monipla_sendMergeCandidateNotification_args {
  static $_TSPEC;

  public $params = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'params',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_SocialAccount',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['params'])) {
        $this->params = $vals['params'];
      }
    }
  }

  public function getName() {
    return 'Monipla_sendMergeCandidateNotification_args';
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
          if ($ftype == TType::LST) {
            $this->params = array();
            $_size262 = 0;
            $_etype265 = 0;
            $xfer += $input->readListBegin($_etype265, $_size262);
            for ($_i266 = 0; $_i266 < $_size262; ++$_i266)
            {
              $elem267 = null;
              $elem267 = new Thrift_SocialAccount();
              $xfer += $elem267->read($input);
              $this->params []= $elem267;
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
    $xfer += $output->writeStructBegin('Monipla_sendMergeCandidateNotification_args');
    if ($this->params !== null) {
      if (!is_array($this->params)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('params', TType::LST, 1);
      {
        $output->writeListBegin(TType::STRUCT, count($this->params));
        {
          foreach ($this->params as $iter268)
          {
            $xfer += $iter268->write($output);
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
