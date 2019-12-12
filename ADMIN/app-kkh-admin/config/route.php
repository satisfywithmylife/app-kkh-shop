<?php
$config['regex_function'] = 'ereg';
$config['404'] = 'Error_Http404';

// test api
$config['mappings']['Test_ApiTest'] = array(
    '^/test/(.*)',
);

//
$config['mappings']['Robots_MP'] = array(
    '^/MP_verify_pwVHO7v8eacIs1PJ.txt',
);

$config['mappings']['Robots_Robots'] = array(
    '^/robots.txt',
);

$config['mappings']['User_Register'] = array(
    '^/user/register',
);

$config['mappings']['User_Login'] = array(
    '^/user/login',
);

$config['mappings']['Article_Add'] = array(
    '^/article/add',
);

$config['mappings']['Article_Del'] = array(
    '^/article/delete',
);

$config['mappings']['Article_Edit'] = array(
    '^/article/edit',
);

$config['mappings']['Article_View'] = array(
    '^/article/view',
);

$config['mappings']['Article_Search'] = array(
	'^/article/search',
);

$config['mappings']['Apost_Add'] = array(
    '^/apost/add',
);

$config['mappings']['Apost_Del'] = array(
    '^/apost/del',
);

$config['mappings']['Apost_Edit'] = array(
    '^/apost/edit',
);

$config['mappings']['Apost_View'] = array(
    '^/apost/view',
);

$config['mappings']['Apost_List'] = array(
    '^/apost/list',
);

$config['mappings']['Upload_UploadFile'] = array(
    '^/upload/file',
);

//comment 获取商品列表
$config['mappings']['Comment_ProductList'] = array(
    '^/comment/productlist',
);

//comment externalinfo
$config['mappings']['Comment_ExternalInfo'] = array(
	'^/comment/externalinfo',
);

//comment 外部评论 - 获取评论来源列表
//$config['mappings']['Comment_SourceList'] = array(
//    '^/comment/sourcelist',
//);

//comment 获取商品的评论
//$config['mappings']['Comment_Get'] = array(
//	'^/comment/get',
//);

//get external comment
$config['mappings']['Comment_GetExternal'] = array(
	'^/comment/getexternal',
);

//get nature comment
$config['mappings']['Comment_GetNature'] = array(
	'^/comment/getnature',
);

//comment 展示/隐藏评论
$config['mappings']['Comment_Display'] = array(
	'^/comment/display',
);

//comment 导入外部评论
$config['mappings']['Comment_ImportExternal'] = array(
	'^/comment/importexternal',
);

//查看运营商品图片
$config['mappings']['Operation_MiddleImg'] = array(
    '^/operation/middleimg',
);
//修改运营商品图片
$config['mappings']['Operation_Edit'] = array(
    '^/operation/edit',
);

$config['mappings']['Search_Add'] = array(
    '^/search/addkwd',
);

$config['mappings']['Search_Del'] = array(
    '^/search/delkwd',
);

$config['mappings']['Search_List'] = array(
    '^/search/listkwd',
);

$config['mappings']['Cornertags_Add'] = array(
    '^/cornertags/add',
);

$config['mappings']['Cornertags_Del'] = array(
    '^/cornertags/delete',
);

$config['mappings']['Cornertags_List'] = array(
    '^/cornertags/list',
);

$config['mappings']['Cornertags_View'] = array(
    '^/cornertags/view',
);

$config['mappings']['Cornertags_Edit'] = array(
    '^/cornertags/edit',
);

//查看青苹果精选、口碑商品、新品推荐商品
$config['mappings']['Shop_ShopList'] = array(
    '^/shop/shoplist',
);
//添加青苹果精选、口碑商品、新品推荐商品
$config['mappings']['Shop_ShopAdd'] = array(
    '^/shop/shopadd',
);
//删除青苹果精选、口碑商品、新品推荐商品
$config['mappings']['Shop_ShopDel'] = array(
     '^/shop/shopdel',
);
//修改限时团购信息
$config['mappings']['Groupon_GroupEdit'] = array(
      '^/groupon/groupedit',
);
//查询限时团购信息
$config['mappings']['Groupon_GetLimitTime'] = array(
      '^/groupon/getlimittime',
);

// 后台订单 - 获取订单列表
$config['mappings']['Order_Get'] = array(
    '^/order/get',
);
// 后台订单 - 获取操作记录列表
$config['mappings']['Order_OperationLog'] = array(
    '^/order/operationlog',
);
// 后台订单 - 修改订单状态
$config['mappings']['Order_ModifyOrderStatus'] = array(
    '^/order/modifyorderstatus',
);
// 后台订单 - 修改订单备注
$config['mappings']['Order_ModifyNote'] = array(
    '^/order/modifynote',
);
// 后台订单 - 导出
$config['mappings']['Order_Export'] = array(
    '^/order/export',
);
// 后台订单 - 获取订单来源列表 和 订单类型列表
$config['mappings']['Order_SourceAndTypeList'] = array(
    '^/order/sourceandtypelist',
);

//获取标题下文章信息 标题内容
$config['mappings']['Article_HeadLineList'] = array(
     '^/article/headlinelist',
);

//添加大标题下的文章
$config['mappings']['Article_HeadListAdd'] = array(
      '^/article/headlistadd',
);

//删除大标题下的文章
$config['mappings']['Article_HeadListDel'] = array(
       '^/article/headlistdel',
);

$config['mappings']['Salerank_Add'] = array(
       '^/salerank/add',
);

$config['mappings']['Salerank_Del'] = array(
       '^/salerank/del',
);

$config['mappings']['Salerank_List'] = array(
       '^/salerank/list',
);

$config['mappings']['Salerank_ProductList'] = array(
       '^/salerank/prodlist',
);

$config['mappings']['Groupon_List'] = array(
		'^/groupon/nowlist',
);

// 自动售货柜 - 医院 - 省列表
$config['mappings']['Cabinet_HospitalProvince'] = array(
    '^/cabinet/hospitalprovince',
);

// 自动售货柜 - 医院 - 市列表
$config['mappings']['Cabinet_HospitalArea'] = array(
    '^/cabinet/hospitalarea',
);

// 自动售货柜 - 医院 - 医院列表
$config['mappings']['Cabinet_HospitalList'] = array(
    '^/cabinet/hospitallist',
);

// 自动售货柜 - 售货柜 - 申请激活
$config['mappings']['Cabinet_CabinetAskForActive'] = array(
    '^/cabinet/cabinetaskforactive',
);

// 自动售货柜 - 售货柜 - 激活
$config['mappings']['Cabinet_CabinetActive'] = array(
    '^/cabinet/cabinetactive',
);

// 自动售货柜 - 售货柜 - 获取
$config['mappings']['Cabinet_CabinetGet'] = array(
    '^/cabinet/cabinetget',
);

// 自动售货柜 - 售货柜 - 编辑
$config['mappings']['Cabinet_CabinetEdit'] = array(
    '^/cabinet/cabinetedit',
);

// 自动售货柜 - 库存 - 获取(商品管理)
$config['mappings']['Cabinet_StockGet'] = array(
    '^/cabinet/stockget',
);

// 自动售货柜 - 库存 - 编辑(编辑商品)
$config['mappings']['Cabinet_StockEdit'] = array(
    '^/cabinet/stockedit',
);

// 自动售货柜 - 入库 - 新增(新建商品)
$config['mappings']['Cabinet_StockInAdd'] = array(
    '^/cabinet/stockinadd',
);

// 自动售货柜 - 入库 - 获取进销存库存中的商品列表
$config['mappings']['Cabinet_StockInProductList'] = array(
    '^/cabinet/stockinproductlist',
);

// 自动售货柜 - 入库 - 获取某商品信息(新建商品)
$config['mappings']['Cabinet_StockInProductInfo'] = array(
    '^/cabinet/stockinproductinfo',
);

// 自动售货柜 - 出库 - 获取(业绩管理)
$config['mappings']['Cabinet_StockOutGet'] = array(
    '^/cabinet/stockoutget',
);

// 自动售货柜 - 出库 - 获取单笔出库详情(业绩详情)
$config['mappings']['Cabinet_StockOutDetail'] = array(
    '^/cabinet/stockoutdetail',
);

// 自动售货柜 - 出库 - 获取单笔出库详情(业绩详情)
$config['mappings']['Cabinet_StockOutExport'] = array(
    '^/cabinet/stockoutexport',
);

// 自动售货柜 - 货道管理 - 获取
$config['mappings']['Cabinet_CounterGet'] = array(
    '^/cabinet/counterget',
);

// 自动售货柜 - 货道管理 - 获取商品信息列表
$config['mappings']['Cabinet_CounterProductList'] = array(
    '^/cabinet/counterproductlist',
);

// 自动售货柜 - 货道管理 - 增加商品数量
$config['mappings']['Cabinet_CounterAdd'] = array(
    '^/cabinet/counteradd',
);

// 自动售货柜 - 货道管理 - 分配商品
$config['mappings']['Cabinet_CounterAssign'] = array(
    '^/cabinet/counterassign',
);

// 自动售货柜 - 货道管理 - 清除某位置的商品
$config['mappings']['Cabinet_CounterClear'] = array(
    '^/cabinet/counterclear',
);

// 自动售货柜 - 售货柜机器库存 - 获取某商品当前售货柜机器的可用库存数量 - 后端间接口
$config['mappings']['Cabinet_CounterGetOne'] = array(
    '^/cabinet/counterone',
);

// 自动售货柜 - 库存 - 加锁 - 后端间接口
$config['mappings']['Cabinet_StockLock'] = array(
    '^/cabinet/stocklock',
);

// 自动售货柜 - 库存 - 解锁 - 后端间接口
$config['mappings']['Cabinet_StockUnlock'] = array(
    '^/cabinet/stockunlock',
);

// 自动售货柜 - 出库 - 计算出货口位置和商品数量 - 后端间接口
$config['mappings']['Cabinet_StockOutCompute'] = array(
    '^/cabinet/stockoutcompute',
);

// 自动售货柜 - 出库 - 出库成功 - 后端间接口
$config['mappings']['Cabinet_StockOutSuccess'] = array(
    '^/cabinet/stockoutsuccess',
);

// 自动售货柜 - 售货柜机器库存 - 商品列表 - 后端间接口
$config['mappings']['Cabinet_CounterList'] = array(
    '^/cabinet/counterlist',
);

// 自动售货柜 - swoole - test
$config['mappings']['Cabinet_CounterSwoole'] = array(
    '^/cabinet/counterswoole',
);

$config['mappings']['Order_GetConfig'] = array(
	'^/order/ordertype',
);

$config['mappings']['Article_AddNew'] = array(
	'^/article/geturl',
);
