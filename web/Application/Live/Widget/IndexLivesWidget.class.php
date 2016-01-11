<?php
namespace Live\Widget;

use Think\Action;

/**
 * 分类widget
 * 用于动态调用分类信息
 */
class IndexLivesWidget extends Action
{
    /* 显示指定分类的同级分类或子分类列表 */
    public function lists($map = '', $order = 'sort desc')
    {
    
      // $lives = S('live_index_hot');
        if (empty($lives)) {
        	$map['play_status']=1;
            $lives = D('Live')->getLivesInfo($map,20,'update_time desc');
            S('live_index_hot', $lives, 3000);
        }
        $this->assign('lives', $lives);
        $this->display('Widget/Index/lives');
    }
}