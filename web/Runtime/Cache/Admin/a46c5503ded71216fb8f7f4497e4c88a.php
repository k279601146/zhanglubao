<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo ($meta_title); ?>|群挑管理平台</title>
<link href="/Public/favicon.ico" type="image/x-icon"
	rel="shortcut icon">
<link rel="stylesheet" type="text/css" href="/Public/Admin/css/base.css"
	media="all">
<link rel="stylesheet" type="text/css" href="/Public/Admin/css/common.css"
	media="all">
<link rel="stylesheet" type="text/css" href="/Public/Admin/css/module.css">
<link rel="stylesheet" type="text/css" href="/Public/Admin/css/style.css"
	media="all">
<link rel="stylesheet" type="text/css"
	href="/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">

<!--[if lt IE 9]> 
<script type="text/javascript" src="/Public/static/jquery-1.11.1.min.js"></script>
 <![endif]-->

<!--[if gte IE 9]><!-->
<script type="text/javascript" src="/Public/static/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/jquery.mousewheel.js"></script>
<!--<![endif]-->




</head>
<body>
<!-- 头部 -->
<div class="header"><!-- Logo --> <a
	href="<?php echo U('Home/Index/index');?>" title="回到前台" target="_blank"><span
	class="logo"></span></a> <!-- /Logo --> <!-- 主导航 -->
<ul class="main-nav">
	<?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>
<!-- /主导航 --> <!-- 用户栏 -->
<div class="user-bar"><a href="javascript:;" class="user-entrance"><i
	class="icon-user"></i></a>
<ul class="nav-list user-menu hidden">
	<li class="manager">你好，<em
		title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></li>
	<li><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
	<li><a href="<?php echo U('User/updateNickname');?>">修改昵称</a></li>
	<li><a href="<?php echo U('Public/logout');?>">退出</a></li>
</ul>
</div>
</div>
<!-- /头部 -->

<!-- 边栏 -->
<div class="sidebar"><!-- 子导航 --> 
<div id="subnav" class="subnav"><?php if(!empty($_extra_menu)): ?> <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?> <?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 --> <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
<ul class="side-sub-menu">
	<?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li><a class="item" href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul><?php endif; ?> <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?></div>
 <!-- /子导航 --></div>
<!-- /边栏 -->

<!-- 内容区 -->
<div id="main-content">
<div id="top-alert" class="fixed alert alert-error"
	style="display: none;">
<button class="close fixed" style="margin-top: 4px;">&times;</button>
<div class="alert-content">这是内容</div>
</div>
<div id="main" class="main"> <!-- nav -->
<?php if(!empty($_show_nav)): ?><div class="breadcrumb"><span>您的位置:</span> <?php $i = '1'; ?> <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span> <?php else: ?> <span><a
	href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?> <?php $i = $i+1; endforeach; endif; ?></div><?php endif; ?> <!-- nav -->  
<div class="main-title">
	<h2><?php echo isset($video['id'])?'编辑':'新增';?>视频</h2>
</div>
<form action="<?php echo U();?>" method="post" class="form-horizontal">
	<div class="form-item">
		<label class="item-label">视频地址<span class="check-tips">（支持youku.com
				qq.com tudou.com）</span></label>
		<div class="controls">
			<input type="text" disabled="disabled" class="text input-large"
				id="video_url" name="video_url"
				value="<?php echo ((isset($video["video_url"]) && ($video["video_url"] !== ""))?($video["video_url"]):''); ?>">
		</div>


		<div class="form-item">
			<label class="item-label">视频图标</label> <input type="hidden"
				name="cover" id="cover" value="<?php echo ((isset($video['cover']) && ($video['cover'] !== ""))?($video['cover']):''); ?>" />
			<div class="upload-img-box">
				<?php if(!empty($video['cover'])): ?><div class="upload-pre-item">
					<img src="<?php echo (get_cover($video["cover"],'url')); ?>" />
				</div><?php endif; ?>
			</div>
		</div>


	</div>
	<div class="form-item">
		<label class="item-label">归属游戏<span class="check-tips">（归属游戏）</span></label>
		<div class="controls">
			<select name="game_id">
				<?php if(is_array($games)): $i = 0; $__LIST__ = $games;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$game): $mod = ($i % 2 );++$i;?><option value="<?php echo ($game["id"]); ?>"
					<?php if(($video['game_id']) == $game["id"]): ?>selected<?php endif; ?>
					><?php echo ($game["title_show"]); ?>
				</option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
	</div>
	<div class="form-item">
		<label class="item-label">视频标题<span class="check-tips">视频标题</span></label>
		<div class="controls">
			<input type="text" class="text input-large" name="title" id="title"
				value="<?php echo ((isset($video["title"]) && ($video["title"] !== ""))?($video["title"]):''); ?>">
		</div>
	</div>

	<div class="form-item">
		<label class="item-label">视频标签<span class="check-tips">视频标签</span></label>
		<div class="controls">
			<input type="text" class="text input-large" name="tags"
				value="<?php echo ((isset($video["tags"]) && ($video["tags"] !== ""))?($video["tags"]):''); ?>">
		</div>
	</div>

	<div class="form-item">
		<label class="item-label">编辑标签<span class="check-tips">编辑标签</span></label>
		<div class="controls">
			<select name="edit_status">

				<option value="0"
					<?php if(($video['edit_status']) == "0"): ?>selected<?php endif; ?>>
					不推荐
				</option>
				<option value="1"
					<?php if(($video['edit_status']) == "1"): ?>selected<?php endif; ?>>
					推荐
				</option>

			</select>
		</div>
	</div>

	<div class="form-item">
		<label class="item-label">视频介绍<span class="check-tips">（视频介绍）</span></label>
		<div class="controls">
			<label class="textarea input-large"> <textarea
					name="description"><?php echo ((isset($video["description"]) && ($video["description"] !== ""))?($video["description"]):''); ?></textarea>
			</label>
		</div>
	</div>
	<div class="form-item">
		<input type="hidden" name="id" value="<?php echo ((isset($video["id"]) && ($video["id"] !== ""))?($video["id"]):''); ?>">
		<input type="hidden" name="cover" value="<?php echo ((isset($video["cover"]) && ($video["cover"] !== ""))?($video["cover"]):''); ?>">
		<input type="hidden" name="flash_url" id="flash_url"
			value="<?php echo ((isset($video["flash_url"]) && ($video["flash_url"] !== ""))?($video["flash_url"]):''); ?>">
		<button class="btn submit-btn ajax-post" id="submit" type="submit"
			target-form="form-horizontal">确 定</button>
		<button class="btn btn-return"
			onclick="javascript:history.back(-1);return false;">返 回</button>
	</div>
</form>
</div>
<div class="cont-ft">
<div class="copyright">
<div class="fl">感谢使用<a href="http://www.quntiao.com/"
	target="_blank">群挑网</a>管理平台</div>
<div class="fr">V<?php echo (QUNTIAO_VERSION); ?></div>
</div>
</div>
</div>
<!-- /内容区 -->
<script type="text/javascript">
    (function(){
        var ThinkPHP = window.Think = {
            "ROOT"   : "", //当前网站地址
            "APP"    : "/index.php?s=", //当前项目地址
            "PUBLIC" : "/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
<script type="text/javascript" src="/Public/static/think.js"></script>
<script type="text/javascript" src="/Public/Admin/js/common.js"></script>
<script type="text/javascript">
        +function(){
            var $window = $(window), $subnav = $("#subnav"), url;
            $window.resize(function(){
                $("#main").css("min-height", $window.height() - 130);
            }).resize();

            /* 左边菜单高亮 */
            url = window.location.pathname + window.location.search;
            url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
            $subnav.find("a[href='" + url + "']").parent().addClass("current");

            /* 左边菜单显示收起 */
            $("#subnav").on("click", "h3", function(){
                var $this = $(this);
                $this.find(".icon").toggleClass("icon-fold");
                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
                      prev("h3").find("i").addClass("icon-fold").end().end().hide();
            });

            $("#subnav h3 a").click(function(e){e.stopPropagation()});

            /* 头部管理员菜单 */
            $(".user-bar").mouseenter(function(){
                var userMenu = $(this).children(".user-menu ");
                userMenu.removeClass("hidden");
                clearTimeout(userMenu.data("timeout"));
            }).mouseleave(function(){
                var userMenu = $(this).children(".user-menu");
                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
            });

	        /* 表单获取焦点变色 */
	        $("form").on("focus", "input", function(){
		        $(this).addClass('focus');
	        }).on("blur","input",function(){
				        $(this).removeClass('focus');
			        });
		    $("form").on("focus", "textarea", function(){
			    $(this).closest('label').addClass('focus');
		    }).on("blur","textarea",function(){
			    $(this).closest('label').removeClass('focus');
		    });

            // 导航栏超出窗口高度后的模拟滚动条
            var sHeight = $(".sidebar").height();
            var subHeight  = $(".subnav").height();
            var diff = subHeight - sHeight; //250
            var sub = $(".subnav");
            if(diff > 0){
                $(window).mousewheel(function(event, delta){
                    if(delta>0){
                        if(parseInt(sub.css('marginTop'))>-10){
                            sub.css('marginTop','0px');
                        }else{
                            sub.css('marginTop','+='+10);
                        }
                    }else{
                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
                            sub.css('marginTop','-'+(diff-10));
                        }else{
                            sub.css('marginTop','-='+10);
                        }
                    }
                });
            }
        }();
    </script>
 <script type="text/javascript">
        //导航高亮
        highlight_subnav('<?php echo U('User/index');?>');
    </script> <script type="text/javascript">
$(function(){
       $('.video_get').click(function(){
    	  var url=$("#video_url").val();
    	   $.post("<?php echo U('Video/getVideoInfo');?>",{link:url}).success(function(data){
		    	var src = '';
		    	
		        if(data.status){
		        	$("#cover_url").val(data.image_url);
		        	$("#flash_url").val(data.flash_url);
		         	$("#title").val(data.title);
		        	$("#cover_div").html(
		        		'<div class="upload-pre-item"><img src="' + data.image_url + '"/></div>'
		        	);
		        } else {
		        	updateAlert(data.info);
		        	setTimeout(function(){
		                $('#top-alert').find('button').click();
		                $(that).removeClass('disabled').prop('disabled',false);
		            },1500);
		        }
    	   });
       }
       )
    });
    </script> 
</body>
</html>