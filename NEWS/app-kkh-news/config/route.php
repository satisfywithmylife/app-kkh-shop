<?php
$config['regex_function'] = 'ereg';
$config['404'] = 'Error_Http404';

// test api
$config['mappings']['Test_ApiTest'] = array(
    '^/test/(.*)',
);

$config['mappings']['Robots_Robots'] = array(
    '^/robots.txt',
);

$config['mappings']['Wxmp_CallBack'] = array(
    '^/wxmp/callback',
);

$config['mappings']['User_Authorize'] = array(
    '^/wxmp/authorize',
);

$config['mappings']['User_MinRegister'] = array(
    '^/wxmp/register',
);

$config['mappings']['Channel_List'] = array(
    '^/channel/list',
);

$config['mappings']['News_List'] = array(
    '^/news/list',
);

$config['mappings']['News_Detail'] = array(
    '^/news/detail',
);

$config['mappings']['Comment_List'] = array(
    '^/comment/list',
);

$config['mappings']['Comment_Add'] = array(
    '^/comment/add',
);

$config['mappings']['Video_List'] = array(
    '^/video/list',
);

$config['mappings']['Video_Detail'] = array(
    '^/video/detail',
);

$config['mappings']['Check_Status'] = array(
    '^/get/status',
);

$config['mappings']['Check_List'] = array(
    '^/beauty/list',
);

$config['mappings']['Image_Make'] = array(
    '^/image/make',
);
