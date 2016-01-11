<?php if (!defined('THINK_PATH')) exit();?>
<div class="col-xs-12  column-content">
	<div class="column-content-title">
		<i class="title-tip-box"></i> <span class="title-tip"><a
			href="<?php echo U('/video/index');?>">最新比赛视频</a></span>  <span class="pull-right"> <a href="<?php echo U('/video/index');?>">更多联赛</a></span>
	</div>
</div>
<div class="col-xs-12 column-content">
	<div class="video-list">
		<ul>
			<?php if(is_array($videos)): $i = 0; $__LIST__ = $videos;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$video): $mod = ($i % 2 );++$i;?><li>
				<div class="video-thumb">
					<a href="<?php echo U('/video/'.$video['id']);?>"> <img
						src="/Public/Core/images/placeholder.png"
						lazy-src="<?php echo query_picture('url',$video['cover']);?>"
						alt="<?php echo ($video['title']); ?>" width="216px" height="120px">
					</a>
					<div class="video-name">
						<span><?php echo ($video['title']); ?></span>
					</div>
				</div>
				<div class="video-des">
					<span class="pull-left"> <span
						class="glyphicon glyphicon-facetime-video "></span> <span
						class="game-des-online"><?php echo ($video['views']); ?>人</span></span> <span
						class="pull-right "> <?php echo get_game($video['game_id'],'title');?>
					</span>
				</div>
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
</div>