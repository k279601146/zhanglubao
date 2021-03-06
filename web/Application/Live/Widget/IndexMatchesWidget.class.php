<?php
namespace Live\Widget;

use Think\Action;

/**
 * 分类widget
 * 用于动态调用分类信息
 */
class IndexMatchesWidget extends Action
{
    /* 显示指定分类的同级分类或子分类列表 */
    public function lists($map = '', $order = 'sort desc')
    {
    
      // $matches = S('live_index_matches');
        if (empty($matches)) {
            $matches = D('Match')->getMatchesInfo($map,5);
            S('live_index_matches', $matches, 3000);
        }
        $this->assign('matches', $matches);
        $this->display('Widget/Index/matches');
    }
}