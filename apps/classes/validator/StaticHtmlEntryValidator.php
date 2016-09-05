<?php

class StaticHtmlEntryValidator {

    public $ValidatorDefinition = array(
        'page_url' => array(
            'type' => 'str',
            'length' => 20,
        ),
        'title' => array(
            'required' => true,
            'type' => 'str',
            'length' => 100,
        ),
        'write_type' => array(
            'required' => true,
            'type' => 'num',
            'range' => array(
                '<=' => 2,
                '>=' => 1
            )
        ),
        'display_flg' => array(
            'type' => 'num'
        ),
        'meta_title' => array(
            'type' => 'str',
            'length' => 60
        ),
        'meta_description' => array(
            'type' => 'str',
            'length' => '511'
        ),
        'meta_keyword' => array(
            'type' => 'str',
            'length' => '511'
        ),
        'public_time_hh' => array(
            'type' => 'num',
            'range' => array(
                '<' => 24,
                '>=' => 0,
            )
        ),
        'public_time_mm' => array(
            'type' => 'num',
            'range' => array(
                '<' => 60,
                '>=' => 0,
            )
        )
    );

    /**
     * @return array
     */
    public function getValidatorDefinition($post) {
        if ($post['write_type'] == StaticHtmlEntries::WRITE_TYPE_BLOG) {
            $this->ValidatorDefinition['body'] = array(
                'required' => true,
                'type' => 'str'
            );
            if (array_key_exists('extra_body', $post)) {
                $this->ValidatorDefinition['extra_body'] = array(
                    'required' => true,
                    'type' => 'str'
                );
            }
        }
        if ($post['layout_type'] == StaticHtmlEntries::LAYOUT_PLAIN) {
            $this->ValidatorDefinition['write_type'] = array(
                'type' => 'num',
                'range' => array(
                    '<=' => 2,
                    '>=' => 1
                )
            );
        }

        return $this->ValidatorDefinition;
    }

    /**
     * @param array $ValidatorDefinition
     */
    public function setValidatorDefinition($ValidatorDefinition) {
        $this->ValidatorDefinition = $ValidatorDefinition;
    }

}
