<?php
/**
 *  @generated
 */
class Thrift_UserAttributeMaster {
  static $_TSPEC;

  public $id = null;
  public $categoryId = null;
  public $require = null;
  public $question = null;
  public $description = null;
  public $dataType = null;
  public $choiceType = null;
  public $choices = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'id',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'categoryId',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'require',
          'type' => TType::I16,
          ),
        4 => array(
          'var' => 'question',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'description',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'dataType',
          'type' => TType::I16,
          ),
        7 => array(
          'var' => 'choiceType',
          'type' => TType::I16,
          ),
        8 => array(
          'var' => 'choices',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['id'])) {
        $this->id = $vals['id'];
      }
      if (isset($vals['categoryId'])) {
        $this->categoryId = $vals['categoryId'];
      }
      if (isset($vals['require'])) {
        $this->require = $vals['require'];
      }
      if (isset($vals['question'])) {
        $this->question = $vals['question'];
      }
      if (isset($vals['description'])) {
        $this->description = $vals['description'];
      }
      if (isset($vals['dataType'])) {
        $this->dataType = $vals['dataType'];
      }
      if (isset($vals['choiceType'])) {
        $this->choiceType = $vals['choiceType'];
      }
      if (isset($vals['choices'])) {
        $this->choices = $vals['choices'];
      }
    }
  }

  public function getName() {
    return 'Thrift_UserAttributeMaster';
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
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->id);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->categoryId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->require);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->question);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->description);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->dataType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->choiceType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->choices);
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
    $xfer += $output->writeStructBegin('Thrift_UserAttributeMaster');
    if ($this->id !== null) {
      $xfer += $output->writeFieldBegin('id', TType::I64, 1);
      $xfer += $output->writeI64($this->id);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->categoryId !== null) {
      $xfer += $output->writeFieldBegin('categoryId', TType::I64, 2);
      $xfer += $output->writeI64($this->categoryId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->require !== null) {
      $xfer += $output->writeFieldBegin('require', TType::I16, 3);
      $xfer += $output->writeI16($this->require);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->question !== null) {
      $xfer += $output->writeFieldBegin('question', TType::STRING, 4);
      $xfer += $output->writeString($this->question);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->description !== null) {
      $xfer += $output->writeFieldBegin('description', TType::STRING, 5);
      $xfer += $output->writeString($this->description);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->dataType !== null) {
      $xfer += $output->writeFieldBegin('dataType', TType::I16, 6);
      $xfer += $output->writeI16($this->dataType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->choiceType !== null) {
      $xfer += $output->writeFieldBegin('choiceType', TType::I16, 7);
      $xfer += $output->writeI16($this->choiceType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->choices !== null) {
      $xfer += $output->writeFieldBegin('choices', TType::STRING, 8);
      $xfer += $output->writeString($this->choices);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
