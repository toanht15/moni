<?php
/**
 * @package AAFramework
 * @subpackage aafwBusinessEntityBase
 * @author Akira Takahashi
 */

class aafwBusinessEntityBase{

	const SAVE_METHOD_PREFIX	= 'save';
	
	public $related_entity_list = array();
	
	public function __call($name, $arguments){
		
		if(preg_match('/^' . self::SAVE_METHOD_PREFIX . '/', $name) > 0 ){
			
			try{
				
				//トランザクションを掛ける
				foreach($this->related_entity_list as $entity){
					
					$entity->db_write->begin();
				
				}
				
				//メソッド実行
				//例) $businessEntity->saveAll();  --->  $businessEntity->__saveAll();
				$method_name = '__' . $name;
				$this->$method_name($arguments);
				
				//コミット
				foreach($this->related_entity_list as $entity){
					
					$entity->db_write->commit();
				
				}
				
			}catch(Exception $e){
				
				//失敗したらロールバック
				foreach($this->related_entity_list as $entity){
					
					$entity->db_write->rollback();
				
				}
				
			}
			
		}
		
	}
	
}


?>