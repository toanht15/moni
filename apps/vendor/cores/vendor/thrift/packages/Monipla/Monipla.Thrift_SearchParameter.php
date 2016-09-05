<?php
/**
 *  @generated
 */
class Thrift_SearchParameter {
  static $_TSPEC;

  public $tableName = null;
  public $query = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'tableName',
          'type' => TType::I32,
          ),
        2 => array(
          'var' => 'query',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['tableName'])) {
        $this->tableName = $vals['tableName'];
      }
      if (isset($vals['query'])) {
        $this->query = $vals['query'];
      }
    }
  }

  public function getName() {
    return 'Thrift_SearchParameter';
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
            $xfer += $input->readI32($this->tableName);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->query);
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
    $xfer += $output->writeStructBegin('Thrift_SearchParameter');
    if ($this->tableName !== null) {
      $xfer += $output->writeFieldBegin('tableName', TType::I32, 1);
      $xfer += $output->writeI32($this->tableName);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->query !== null) {
      $xfer += $output->writeFieldBegin('query', TType::STRING, 2);
      $xfer += $output->writeString($this->query);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
