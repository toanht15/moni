<?php
/**
 *  @generated
 */
class Monipla_mergeAccount_args {
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
    return 'Monipla_mergeAccount_args';
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
            $_size255 = 0;
            $_etype258 = 0;
            $xfer += $input->readListBegin($_etype258, $_size255);
            for ($_i259 = 0; $_i259 < $_size255; ++$_i259)
            {
              $elem260 = null;
              $elem260 = new Thrift_SocialAccount();
              $xfer += $elem260->read($input);
              $this->params []= $elem260;
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
    $xfer += $output->writeStructBegin('Monipla_mergeAccount_args');
    if ($this->params !== null) {
      if (!is_array($this->params)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('params', TType::LST, 1);
      {
        $output->writeListBegin(TType::STRUCT, count($this->params));
        {
          foreach ($this->params as $iter261)
          {
            $xfer += $iter261->write($output);
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
