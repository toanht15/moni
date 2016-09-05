<?php

class CpActionDeadLineValidator extends BaseValidator {

    private $endType;
    private $endAt;
    private $endHour;
    private $endMinute;
    private $isLoginManager;

    private $validatorDefinition = [
        'end_date' => [
            'type' => 'str',
            'length' => 10,
        ],
        'end_hh' => [
            'type' => 'num',
            'range' => [
                '<' => 24,
                '>=' => 0,
            ],
        ],
        'end_mm' => [
            'type' => 'num',
            'range' => [
                '<' => 60,
                '>=' => 0,
            ],
        ],
        'end_type' => [
            'type' => 'num'
        ],
    ];

    public function __construct(
        $endType,
        $endDate,
        $endHour,
        $endMinute,
        $isLoginManager=false
    ) {
        parent::__construct();

        $this->endType = $endType;
        $this->endDate = $endDate;
        $this->endHour = $endHour;
        $this->endMinute = $endMinute;
        $this->isLoginManager = $isLoginManager;
    }

    public function validate() {
        // クライアントにも開放することになったので、コメントアウトしておく
        //if (!$this->isLoginManager) {
        //    return true;
        //}
        // 締め切り日設定
        if ($this->endType == CpAction::END_TYPE_ORIGINAL) {
            $endDatetime =
                $this->endDate .  ' ' .
                $this->endHour .  ':' . $this->endMinute . ':00';
            // 日付の妥当性チェック
            if (!$this->isCorrectDate($endDatetime)) {
                $this->setErrors(['end_datetime' => 'INVALID_TIME1']);
                return false;
            }
        }

        return true;
    }

    /**
     * バリデーション定義の取得
     *
     * @return array
     */
    public function getValidationColumnAndRule() {
        // クライアントにも開放することになったので、コメントアウトしておく
        //if (!$this->isLoginManager) {
        //    return [];
        //}

        return array_merge_recursive(
            $this->getValidatorDefinishionColumn(),
            $this->getValidatorDefinishionRule() 
        );
    }

    public function getValidatorDefinishionColumn() {
        // クライアントにも開放することになったので、コメントアウトしておく
        //if (!$this->isLoginManager) {
        //    return [];
        //}

        return $this->validatorDefinition;
    }

    public function getValidatorDefinishionRule() {
        // クライアントにも開放することになったので、コメントアウトしておく
        //if (!$this->isLoginManager) {
        //    return [];
        //}

        $rule = [
            'end_type' => ['required' => true],
        ];
        if ($this->endType == CpAction::END_TYPE_ORIGINAL) {
            $rule = array_merge($rule, [
                'end_date' => ['required' => true],
                'end_hh'   => ['required' => true],
                'end_mm'   => ['required' => true],
            ]);
        }

        return $rule;
    }

    private function isCorrectDate($date) {
        if (!$date) {
            return false;
        }
        // 日付妥当性の判定
        if (!$this->validateDate($date)) {
            return false;
        }

        $now = new Datetime();
        $dateTime = DateTime::createFromFormat('Y/m/d H:i:s', $date);
        if (!$now || !$dateTime) {
            return false;
        }
        if ($now > $dateTime) {
            return false;
        }

        return true;
    }

    public function validateDate($date, $format = 'Y/m/d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }
}
