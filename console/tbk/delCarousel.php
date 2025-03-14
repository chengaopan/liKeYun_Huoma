<?php

	// 页面编码
	header("Content-type:application/json");
	
	// 判断登录状态
    session_start();
    if(isset($_SESSION["yinliubao"])){
        
        // 已登录
    	$Carousel_id = trim($_GET['Carousel_id']);
    	
        // 过滤参数
        if(empty($Carousel_id) || !isset($Carousel_id)){
            
            $result = array(
			    'code' => 203,
                'msg' => '非法请求'
		    );
        }else{
            
            // 当前登录的用户
            $LoginUser = $_SESSION["yinliubao"];
            
            // 数据库配置
        	include '../Db.php';
        
        	// 实例化类
        	$db = new DB_API($config);
        	
            // 验证当前要删除的 Carousel_id 的发布者是否为当前登录的用户
            $checkUser = $db->set_table('ylb_CarouselSPA')->find(['Carousel_id' => $Carousel_id]);
            $Carousel_create_user = $checkUser['Carousel_create_user'];
            
            // 判断操作结果
            if($Carousel_create_user == $LoginUser){
                
                // 用户一致：允许操作
                // 1. 先删除单页
                $delSPA = $db->set_table('ylb_CarouselSPA')->delete(['Carousel_id' => $Carousel_id]);
                
                // 2. 再删除单页内的轮播图
                $delPics = $db->set_table('ylb_CarouselSPA_pics')->delete(['Carousel_id' => $Carousel_id]);
                
                if($delSPA && $delPics){
                    
                    // 删除成功
                    $result = array(
    			        'code' => 200,
                        'msg' => '已删除'
    		        );
                }else{
                    
                    // 删除失败
                    $result = array(
        			    'code' => 202,
                        'msg' => '删除失败'
        		    );
                }
            }else{
                
                // 用户不一致：禁止操作
                $result = array(
        			'code' => 202,
                    'msg' => '删除失败：禁止操作'
        		);
            }
        }
    }else{
        
        // 未登录
        $result = array(
			'code' => 201,
            'msg' => '未登录'
		);
    }

	// 输出JSON
	echo json_encode($result,JSON_UNESCAPED_UNICODE);
	
?>