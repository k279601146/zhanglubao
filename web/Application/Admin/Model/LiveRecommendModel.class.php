<?php
namespace Admin\Model;
use Think\Model;

class LiveRecommendModel extends Model{

	protected $_validate = array(
	array('title', 'require', '标识不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
	array('live_id', 'require', '标签不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
	);


	protected $_auto = array(
	array('status', 1, self::MODEL_INSERT),
	array('create_time', NOW_TIME, self::MODEL_INSERT),
	);

 


	/**
	 * 更新分类信息
	 * @return boolean 更新状态
	 * @author Rocks
	 */
	public function update(){
		$data = $this->create();
		if(!$data){
			return false;
		}
		if(empty($data['id'])){
			$res = $this->add();
		}else{
			$res = $this->save();

		}
		$this->cleanCache(array($data['id']));
		action_log('update_live_recommend', 'live_recommend', $data['id'] ? $data['id'] : $res, UID);
		return $res;
	}

	/**
	 * 清除指定Game数据
	 *
	 */
	public function cleanCache($ids) {
		if (empty ( $ids )) {
			return false;
		}
		! is_array ( $ids ) && $ids = explode ( ',', $ids );
		foreach ( $ids as $id ) {
			static_cache ( 'live_recommend_info_' . $id, false );
			$keys =S('live_recommend_info_'.$id);
			foreach ($keys as $k){
				S($k,null);
			}
			S('live_recommend_info_'.$id,null);
		}

		return true;
	}

}

