<?php
//$ch = curl_init();

// 设置URL和相应的选项
//curl_setopt($ch, CURLOPT_URL, "http://www.baidu.com/");
//curl_setopt($ch, CURLOPT_HEADER, 0);

// 抓取URL并把它传递给浏览器
//$res = curl_exec($ch);
//Logger::info('test, res = ' . json_encode($res));

// 关闭cURL资源，并且释放系统资源
//curl_close($ch);

        
 //       $postUrl = 'http://spider.prod.kangkanghui.com/jd/product/spider/comment';
 //       $curlPost = array('url' => 'https://item.jd.com/5007538.html', 'size' => 2);
 //       $ch = curl_init();//初始化curl
 //       curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
 //       curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
 //       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
 //       curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
 //       curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
 //       $data = curl_exec($ch);//运行curl
//		echo 'ok11, data = ' . json_encode($data);
 //       curl_close($ch);

		
		$url  = 'https://item.jd.com/5007538.html';
		$size = 2;
		$postUrl = 'http://spider.prod.kangkanghui.com/jd/product/spider/comment';
        $curlPost = array(
            'url' => $url, 
            'size' => $size
        );  

		echo json_encode($curlPost);
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
	//	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
 //       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);//运行curl
		echo 'ok11, data = ' . $data;
 //       Logger::info('ok11, type = ' . gettype($data) . ', type1 = ' . json_decode($data, true) . ', data = ' . $data);
        curl_close($ch);

