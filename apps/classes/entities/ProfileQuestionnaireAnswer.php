<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class ProfileQuestionnaireAnswer extends aafwEntityBase {

    protected $_Relations = array(

        'BrandsUsersRelations' => array(
            'relation_id' => 'id',
        ),

        'ProfileQuestionnaires' => array(
            'question_id' => 'id',
        ),
    );
}