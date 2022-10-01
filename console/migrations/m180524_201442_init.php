<?php
/**
 *  @link http://github.com/zantoto
 *  @copyright Copyright (c) 2020 Zantoto
 *  @author 57620133@qq.com
 */

use common\models\AreaCode;
use common\models\Category;
use common\models\Member;
use common\models\MemberAddress;
use common\models\OrderSku;
use common\models\OrderSkuRefund;
use common\models\Product;
use common\models\Rate;
use common\models\Shop;
use common\models\ShopCategory;
use common\models\Sku;
use common\models\User;
use common\models\Wishlist;
use common\wosotech\helper\Util;
use noam148\imagemanager\models\ImageManager;
use yii\db\Expression;
use yii\db\Migration;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\FileHelper;


class m180524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $faker = \Faker\Factory::create('zh_CN');
        Yii::setAlias('@webroot', Yii::getAlias('@backend') . '/web');
        Yii::setAlias('@web', Yii::getAlias('@backendUrl'));
        $tmpProvinceIds = $provinceIds = array_column(AreaCode::getChina()->getChildren()->asArray()->all(), 'id');

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%revenue_log}}")->execute();
        $this->createTable('{{%revenue_log}}', [
            'id' => $this->bigPrimaryKey(),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('会员ID'),
            'kind' => $this->tinyInteger()->defaultValue(0)->comment('收入类型'),
            'title' => $this->string(128)->comment('来源'), // 摘要
            'amount' => $this->decimal(12, 2)->comment('金额'), // 收入金额
            'memo' => $this->string(128)->comment('备注'),
            'order_id' => $this->string(64)->comment('订单ID'),
            'source_id' => $this->string(64)->comment('收益来源ID'), // 下级的member_id
            'order_sku_id' => $this->integer()->notNull()->defaultValue(0)->comment('订单SKU'),
            'order_amount' => $this->decimal(12, 2)->comment('成交金额'), // 业务成交金额
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'), // 0: 未结算, 1:已结算
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
            'order_time' => $this->timestamp()->defaultValue(null)->comment('发生时间'), // 业务发生时间
            'cashout_status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('提现状态'),
            'pay_type' => $this->smallInteger()->notNull()->defaultValue(0)->comment('提现账户类型'),
            'pay_info' => $this->string()->notNull()->defaultValue('')->comment('提现账号信息'),
            'reason' => $this->string()->notNull()->defaultValue('')->comment('提现驳回理由'),
            'fee' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('手续费'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%revenue_log}}', '余额（含收益）明细');
        $this->createIndex('member_id_created_at', '{{%revenue_log}}', ['member_id', 'created_at']);
        $this->createIndex('order_id', '{{%revenue_log}}', ['order_id']);
        $this->createIndex('order_sku_id', '{{%revenue_log}}', ['order_sku_id']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%shop_category}}")->execute();
        $this->createTable('{{%shop_category}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->comment('父分类'),
            'name' => $this->string(32)->comment('名称'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%shop_category}}', '店铺分类');
        $this->createIndex('parent_id', '{{%shop_category}}', ['parent_id']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%category}}")->execute();
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->comment('父分类'),
            'is_leaf' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('叶子'),
            'name' => $this->string(32)->comment('名称'),
            'keyword' => $this->string(64)->comment('关键词'),
            'description' => $this->string()->comment('描述'),
            'unit' => $this->string(8)->comment('单位'),
            'icon' => $this->string()->comment('图标'),
            'path' => $this->string()->comment('路径'),
            'depth' => $this->integer()->notNull()->defaultValue(0)->comment('层级'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'is_visual' => $this->smallInteger()->notNull()->defaultValue(1)->comment('显示'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%category}}', '商品分类');
        $this->createIndex('parent_id', '{{%category}}', ['parent_id']);

        $model = new \common\models\Category();
        $model->setAttributes([
            'id' => Category::ROOT_ID,
            'name' => '顶层',
        ], true);
        $model->makeRoot()->save();

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%option}}")->execute();
        $this->createTable('{{%option}}', [
            'id' => $this->primaryKey(),
            'type' => $this->integer()->notNull()->defaultValue(0)->comment('类型'), // 0: 下拉选项, 1:文本
            'name' => $this->string(64)->notNull()->defaultValue('')->comment('名称'), // 如颜色
            'alias' => $this->string(64)->notNull()->defaultValue('')->comment('别名'), // 后台配置商品时下拉框使用, 如手机的颜色, 前台显示还是用name
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%option}}', '商品属性'); // option, attribute, property

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%option_value}}")->execute();
        $this->createTable('{{%option_value}}', [
            'id' => $this->primaryKey(), // 属性值的ID, 即option_value_id
            'option_id' => $this->integer()->notNull()->defaultValue(0),
            'name' => $this->string()->notNull()->defaultValue('')->comment('属性值'),
            'image' => $this->string()->comment('图片'), // 比如黑色有黑色的图标, 那如何体现服装的黑色与手机的黑色图片不一样? 只能在sku里输入图片来解决了
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%option_value}}', '属性的值'); // 1个属性对应多个值
        $this->createIndex('option_id', '{{%option_value}}', ['option_id']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%category_option}}")->execute();
        $this->createTable('{{%category_option}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull()->defaultValue(0)->comment('分类ID'),
            'option_id' => $this->integer()->notNull()->defaultValue(0)->comment('属性ID'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%category_option}}', '分类与属性关系表'); // 一个商品一个分类, 一个分类多个属性, 通过关联找到每个商品的属性
        $this->createIndex('category_id_option_id', '{{%category_option}}', ['category_id', 'option_id'], true);
        $this->createIndex('option_id', '{{%category_option}}', ['option_id']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%brand}}")->execute();
        $this->createTable('{{%brand}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull()->defaultValue('')->comment('名称'),
            'description' => $this->string()->notNull()->defaultValue('说明'),
            'letter' => $this->string(1)->notNull()->defaultValue('')->comment('首字母'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%brand}}', '品牌');

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%product}}")->execute();
        $this->createTable('{{%product}}', [
            'id' => $this->string(64)->notNull()->defaultValue('')->comment('商品ID'),
            'spu_id' => $this->string(64)->unique()->comment('商品货号'), // Standard Product Unit
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('商户ID'), // 99999999: 表示平台
            'category_id' => $this->integer()->notNull()->defaultValue(0)->comment('分类ID'),
            'category_path' => $this->string(128)->notNull()->defaultValue('')->comment('分类路径'), // 如1/3/4
            'category_id1' => $this->integer()->notNull()->defaultValue(0)->comment('一级分类'), // 设category_id1, category_id2, category_id3只是便于Search, 只放前3级, 商品最终分类还是放在category_id中
            'category_id2' => $this->integer()->notNull()->defaultValue(0)->comment('二级分类'),
            'category_id3' => $this->integer()->notNull()->defaultValue(0)->comment('三级分类'),
            'title' => $this->string(64)->notNull()->defaultValue('')->comment('标题'),
            'sub_title' => $this->string(128)->notNull()->defaultValue('')->comment('子标题'),
            'brand_id' => $this->string(64)->notNull()->defaultValue('')->comment('品牌'),
            'main_image_id' => $this->string(64)->comment('主图ID'),
            'quantity' => $this->integer()->notNull()->defaultValue(0)->comment('库存'), // 总库存
            'price' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('基础单价'),
            'cost_price' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('成本价'),
            'market_price' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('市场参考价'),
            'sold_volume' => $this->integer()->notNull()->defaultValue(0)->comment('销量'), // 卖出订单数
            'shipping' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('需发货'),
            'has_option' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('简单商品'), // 无属性(无规格)
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status_listing' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('上架状态'),
            'is_platform' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('是平台商品'),
            'detail' => $this->string()->notNull()->defaultValue('')->comment('商品详情'),
            'memo' => $this->string()->notNull()->defaultValue('')->comment('备注'),
            'total_rate_score' => $this->integer()->notNull()->defaultValue(0)->comment('星数'),    // 累计评价分数
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),
            'reason' => $this->string()->notNull()->defaultValue('')->comment('理由'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
            'listing_time' => $this->timestamp()->defaultValue(null)->comment('上架时间'),
            'delisting_time' => $this->timestamp()->defaultValue(null)->comment('下架时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%product}}', '商品'); // SPU表
        $this->addPrimaryKey('id', '{{%product}}', ['id']);
        $this->createIndex('member_id', '{{%product}}', ['member_id']);
        $this->createIndex('category_id', '{{%product}}', ['category_id']);
        $this->createIndex('category_path', '{{%product}}', ['category_path']);
        $this->createIndex('listing_time', '{{%product}}', ['listing_time']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%product_picture}}")->execute();
        $this->createTable('{{%product_picture}}', [
            'id' => $this->primaryKey(),
            'global_sid' => $this->string(64),
            'global_iid' => $this->integer()->notNull()->defaultValue(0)->comment('ID'),
            'path' => $this->string()->comment('文件名'),
            'base_url' => $this->string()->comment('url路径'),
            'type' => $this->string(256)->comment('类型'),
            'size' => $this->integer()->notNull()->defaultValue(0)->comment('尺寸'),
            'name' => $this->string(512)->comment('原文件名'),
            'order' => $this->integer()->comment('排序'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%product_picture}}', '商品图片');
        $this->createIndex('global_iid', '{{%product_picture}}', ['global_iid']);
        $this->createIndex('global_sid', '{{%product_picture}}', ['global_sid']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%product_detail_picture}}")->execute();
        $this->createTable('{{%product_detail_picture}}', [
            'id' => $this->primaryKey(),
            'global_sid' => $this->string(64),
            'global_iid' => $this->integer()->notNull()->defaultValue(0)->comment('ID'),
            'path' => $this->string()->comment('文件名'),
            'base_url' => $this->string()->comment('url路径'),
            'type' => $this->string(256)->comment('类型'),
            'size' => $this->integer()->notNull()->defaultValue(0)->comment('尺寸'),
            'name' => $this->string(512)->comment('原文件名'),
            'order' => $this->integer()->comment('排序'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%product_detail_picture}}', '商品详情图');
        $this->createIndex('global_iid', '{{%product_detail_picture}}', ['global_iid']);
        $this->createIndex('global_sid', '{{%product_detail_picture}}', ['global_sid']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%product_option}}")->execute();
        $this->createTable('{{%product_option}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->string(64)->notNull()->defaultValue('')->comment('商品ID'),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('商户ID'),
            'name' => $this->string(128)->notNull()->defaultValue('')->comment('属性名'),        // 如颜色
            'required' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('必选项'),
            'default_value' => $this->string(64)->notNull()->defaultValue('')->comment('默认值'), // 属性的类型为文本时才有效
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%product_option}}', '商品-属性'); // 后台选择关联属性后(后台勾选出来的属性),生成此表
        $this->createIndex('product_id', '{{%product_option}}', ['product_id']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%product_option_value}}")->execute();
        $this->createTable('{{%product_option_value}}', [
            'id' => $this->primaryKey(),
            'product_option_id' => $this->integer()->notNull()->defaultValue(0)->comment('商户ID'),
            'name' => $this->string(128)->notNull()->defaultValue('')->defaultValue('')->comment('值名称'), // 红色
            'image' => $this->string()->comment('图片'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'price' => $this->decimal(12, 2)->notNull()->defaultValue(1)->comment('价格增长数'), // 0.5 表示增加0.5元, -1.2表示减少1.2元
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%product_option_value}}', '商品-属性-值'); // 表明每个属性有哪些取值
        $this->createIndex('product_option_id', '{{%product_option_value}}', ['product_option_id']);

        // 由product_option_value中的option_id,option_value_id进行组合,
        // 属性1有x个option, 属性2有y, 属性3有z个, 则有x*y*z条记录SKU组合
        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%sku}}")->execute();
        $this->createTable('{{%sku}}', [
            'id' => $this->primaryKey(),
            'sku_code' => $this->string(64)->unique()->comment('SKU编码'),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('商户ID'),
            'product_id' => $this->string(64)->notNull()->defaultValue('')->comment('商品ID'),
            'option_value_ids' => $this->string()->comment('一组属性与值'), // 逗号分隔, 升序, 如[1:1, 2:3],
            'option_value_names' => $this->string(500)->comment('一组属性与值'), // 逗号分隔, 升序, 如[颜色:红,尺码:大]
            'query_string' => $this->string(150)->comment('查询KEY'), // 如1:1, 2:3
            'quantity' => $this->integer()->notNull()->defaultValue(0)->comment('库存'),
            'price' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('单价'),// 未用
            'sold_volume' => $this->integer()->notNull()->defaultValue(0)->comment('SKU销量'), // 未用
            'image' => $this->string()->comment('SKU图片'), // 暂未用
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%sku}}', '商品SKU');
        $this->createIndex('member_id', '{{%sku}}', ['member_id']);
        $this->createIndex('product_id', '{{%sku}}', ['product_id']);
        $this->createIndex('query_string', '{{%sku}}', ['product_id', 'query_string'], true);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%cart}}")->execute();
        $this->createTable('{{%cart}}', [
            'id' => $this->primaryKey(),
            'buyer_id' => $this->integer()->notNull()->defaultValue(0)->comment('买家ID'),
            'product_id' => $this->string(64)->notNull()->defaultValue('')->comment('商品ID'),
            'sku_id' => $this->integer()->notNull()->defaultValue(0)->comment('SKU ID'),
            'sku_code' => $this->string(64)->notNull()->defaultValue('')->comment('SKU编码'),
            'quantity' => $this->integer()->notNull()->defaultValue(0)->comment('数量'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%cart}}', '购物车');
        $this->createIndex('buyer_id', '{{%cart}}', ['buyer_id', 'id']);
        $this->createIndex('sku_code', '{{%cart}}', ['sku_code']);
        $this->createIndex('sku_id', '{{%cart}}', ['sku_id']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%order}}")->execute();
        $this->createTable('{{%order}}', [
            'id' => $this->string(64)->notNull()->defaultValue('')->comment('订单号'),
            'tid' => $this->string(64)->comment('事务ID'),
            'is_platform' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('是平台订单'),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('商户ID'),
            'buyer_id' => $this->integer()->notNull()->defaultValue(0)->comment('买家ID'),
            'mobile' => $this->string(16)->comment('买家手机号'),
            'nickname' => $this->string(64)->comment('买家昵称'),
            'total_amount' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('总金额'),
            'quantity' => $this->integer()->notNull()->defaultValue(0)->comment('数量'),
            'shipping_fee' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('邮费'),
            'pay_amount' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('实付金额'),
            'pay_method' => $this->integer()->notNull()->defaultValue(0)->comment('支付方式'), // 0:余额, 1:支付宝, 2:微信
            'member_address_id' => $this->integer()->notNull()->defaultValue(0)->comment('地址ID'),
            'shipping_area_parent_id' => $this->string(16)->notNull()->defaultValue('')->comment('省'),
            'shipping_area_id' => $this->string(16)->notNull()->defaultValue('')->comment('市'),
            'shipping_district_id' => $this->string(16)->notNull()->defaultValue('')->comment('区'),
            'shipping_address' => $this->string()->notNull()->defaultValue('')->comment('收货地址'),
            'shipping_zipcode' => $this->string(32)->notNull()->defaultValue('')->comment('邮编'),
            'shipping_name' => $this->string(32)->notNull()->defaultValue('')->comment('收货人'),
            'shipping_mobile' => $this->string(32)->notNull()->defaultValue('')->comment('收货人电话'),
            'express_company' => $this->string(32)->notNull()->defaultValue('')->comment('物流公司'),
            'express_code' => $this->string(32)->notNull()->defaultValue('')->comment('运单号'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('订单状态'), // 订单大状态 1: 待付款, 2:已付款待发货, 3. giftkey已付款待指令, 4:已发货, 5: 已收货待评价, 6, 已评价, 7:已关闭, 8: 退货中（所有商品都是退货中）
            'has_refund' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('有退货'), // 此订单中是否含有退货商品
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
            'pay_time' => $this->timestamp()->defaultValue(null)->comment('付款时间'),
            'shipping_time' => $this->timestamp()->defaultValue(null)->comment('发货时间'),
            'confirm_time' => $this->timestamp()->defaultValue(null)->comment('确认收货时间'),
            'rate_time' => $this->timestamp()->defaultValue(null)->comment('评价时间'),
            'memo' => $this->string(2048)->notNull()->defaultValue('')->comment('备注'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%order}}', '订单');
        $this->addPrimaryKey('id', '{{%order}}', ['id']);
        $this->createIndex('member_id', '{{%order}}', ['member_id']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%order_sku}}")->execute();
        $this->createTable('{{%order_sku}}', [
            'id' => $this->primaryKey(),
            'sku_id' => $this->integer()->notNull()->defaultValue(0)->comment('SKU ID'),
            'order_id' => $this->string(64)->comment('订单ID'),
            'product_id' => $this->string(64)->notNull()->defaultValue('')->comment('商品ID'),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('卖家ID'),
            'buyer_id' => $this->integer()->notNull()->defaultValue(0)->comment('买家ID'),
            'sku_code' => $this->string(64)->notNull()->defaultValue('')->comment('SKU编码'),
            'title' => $this->string(64)->notNull()->defaultValue('')->comment('标题'),
            'sub_title' => $this->string(128)->notNull()->defaultValue('')->comment('子标题'),
            'coupon_used' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('收券'),
            'main_image_type' => $this->string()->notNull()->defaultValue('')->comment('图片类型'),
            'main_image' => $this->string()->comment('主图'),
            'main_image_thumb' => $this->string()->comment('主图缩略图'),
            'option_value_names' => $this->string(500)->comment('一组属性与值'), // 逗号分隔, 升序, 如[颜色:红,尺码:大]
            'price' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('购买时单价'),
            'image' => $this->string()->comment('SKU图片'), // 暂未用
            'quantity' => $this->integer()->notNull()->defaultValue(0)->comment('数量'),
            'amount' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('金额'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('商品退货状态'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
            'is_rated' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('已评价'),
            'rate_time' => $this->timestamp()->defaultValue(null)->comment('评价时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%order_sku}}', '订单中的商品');
        $this->createIndex('order_id', '{{%order_sku}}', ['order_id']);
        $this->createIndex('product_id', '{{%order_sku}}', ['product_id']);
        $this->createIndex('sku_id', '{{%order_sku}}', ['sku_id']);
        $this->createIndex('sku_code', '{{%order_sku}}', ['sku_code']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%order_sku_refund}}")->execute();
        $this->createTable('{{%order_sku_refund}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->string(64)->comment('订单ID'),
            'product_id' => $this->string(64)->notNull()->defaultValue('')->comment('商品ID'),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('卖家ID'),
            'buyer_id' => $this->integer()->notNull()->defaultValue(0)->comment('买家ID'),
            'mobile' => $this->string(16)->comment('买家手机号'),
            'nickname' => $this->string(64)->comment('买家昵称'),
            'order_sku_id' => $this->integer()->notNull()->defaultValue(0)->comment('ID')->unique(),
            'need_ship' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('是否需发货'), // 是退货还是退款
            'refund_reason' => $this->string(64)->notNull()->defaultValue('')->comment('退款原因'),
            'refund_detail' => $this->string(500)->notNull()->defaultValue('')->comment('问题描述'),
            'refund_amount' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('退款金额'),
            'refund_coupon' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('退款礼券'),
            'handled_by' => $this->integer()->comment('处理人'),
            'handled_memo' => $this->string(500)->notNull()->defaultValue('')->comment('处理备注'), // 回复
            'shipping_address' => $this->string()->notNull()->defaultValue('')->comment('收货地址'),
            'shipping_zipcode' => $this->string(32)->notNull()->defaultValue('')->comment('邮编'),
            'shipping_name' => $this->string(32)->notNull()->defaultValue('')->comment('收货人'),
            'shipping_mobile' => $this->string(32)->notNull()->defaultValue('')->comment('收货人电话'),
            'shipping_by' => $this->integer()->comment('收货人'),
            'shipping_memo' => $this->string(64)->comment('收货备注'),
            'express_company' => $this->string(32)->notNull()->defaultValue('')->comment('物流公司'),
            'express_code' => $this->string(32)->notNull()->defaultValue('')->comment('运单号'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('退货状态'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
            'handled_time' => $this->timestamp()->defaultValue(null)->comment('处理时间'),
            'shipping_time' => $this->timestamp()->defaultValue(null)->comment('收货时间'),
            'image1' => $this->string()->comment('凭证图片1'),
            'image2' => $this->string()->comment('凭证图片2'),
            'image3' => $this->string()->comment('凭证图片3'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%order_sku_refund}}', '退货信息');
        $this->createIndex('order_id', '{{%order_sku_refund}}', ['order_id']);
        Yii::$app->db->createCommand("ALTER TABLE {{%order_sku_refund}} AUTO_INCREMENT=10000000")->execute();

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%rate}}")->execute();
        $this->createTable('{{%rate}}', [
            'id' => $this->string(64)->notNull()->defaultValue('')->comment('ID'),
            'order_id' => $this->string(64)->comment('订单ID'),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('商户ID'),
            'buyer_id' => $this->integer()->notNull()->defaultValue(0)->comment('用户ID'),
            'avatar_url' => $this->string()->comment('评价者头像'),
            'main_image' => $this->string()->comment('商品主图'),
            'main_image_thumb' => $this->string()->comment('商品缩略图'),
            'product_id' => $this->string(64)->notNull()->defaultValue('')->comment('商品ID'),
            'product_title' => $this->string(64)->comment('商品标题'),
            'order_sku_id' => $this->integer()->comment('订单SKU')->unique(),
            'content' => $this->string(1000)->comment('评价内容'),
            'score' => $this->integer()->notNull()->defaultValue(0)->comment('评价星数'), // 0 - 3
            'ip' => $this->string(18)->comment('IP'),
            'is_anonymous' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('匿名'),
            'is_hidden' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('隐藏'),
            'is_auto' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('是自动好评'),
            'nickname' => $this->string(64)->notNull()->defaultValue('')->comment('买家昵称'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('状态'), // 0: 显示, 1:隐藏
            'sort_order' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('排序'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%rate}}', '商品评价');
        $this->addPrimaryKey('id', '{{%rate}}', ['id']);
        $this->createIndex('product_id', '{{%rate}}', ['product_id']);
        $this->createIndex('order_id', '{{%rate}}', ['order_id']);
        $this->createIndex('member_id', '{{%rate}}', ['member_id']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%tag}}")->execute();
        $this->createTable('{{%tag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->defaultValue('')->comment('Tag'),
            'frequency' => $this->integer()->notNull()->defaultValue(0)->comment('frequency'),
        ]);
        $this->addCommentOnTable('{{%tag}}', '标签');

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%product_tag}}")->execute();
        $this->createTable('{{%product_tag}}', [
            'product_id' => $this->integer()->notNull()->defaultValue(0)->comment('商品ID'),
            'tag_id' => $this->integer()->notNull()->defaultValue(0)->comment('标签ID'),
        ]);
        $this->addCommentOnTable('{{%product_tag}}', '商品标签关系');
        $this->addPrimaryKey('product_id_tag_id', '{{%product_tag}}', ['product_id', 'tag_id']);


        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%auth}}")->execute();
        $this->createTable('{{%auth}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'oauth_client' => $this->string(64)->comment('第三方名称'), // weixin_web, weixin_mp, weixin_app
            'oauth_client_user_id' => $this->string(), // qq ID, weixin ID, ...
            'openid' => $this->string(),
            'nickname' => $this->string(),
            'avatar_url' => $this->string(),
        ], $tableOptions);
        $this->addCommentOnTable('{{%auth}}', '第三方登录账号');
        $this->createIndex('oauth_client', '{{%auth}}', ['oauth_client', 'oauth_client_user_id(128)'], true);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%user_profile}}")->execute();
        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%user}}")->execute();
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(64)->notNull()->defaultValue('')->comment('账号'),
            'auth_key' => $this->string(64)->notNull(),
            'password_plain' => $this->string()->notNull()->defaultValue('')->comment('密码'),
            'password_hash' => $this->string()->comment('密码'),
            'password_reset_token' => $this->string(),
            'email' => $this->string(64)->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(User::STATUS_ACTIVE),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
            'logged_at' => $this->timestamp()->defaultValue(null)->comment('最近登录'),
            'mobile' => $this->string(16)->notNull()->defaultValue('')->comment('手机'),
            'name' => $this->string(16)->comment('姓名'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%user}}', '后台用户');

        $this->createTable('{{%user_profile}}', [
            'user_id' => $this->primaryKey(),
            'firstname' => $this->string(),
            'middlename' => $this->string(),
            'lastname' => $this->string(),
            'avatar_path' => $this->string(),
            'avatar_base_url' => $this->string(),
            'locale' => $this->string(32)->notNull(),
            'gender' => $this->smallInteger(1)
        ], $tableOptions);

        $this->insert('{{%user}}', [
            'id' => 1,
            'username' => 'webmaster',
            'email' => 'webmaster@example.com',
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('webmaster'),
            'name' => 'Jack',
            'mobile' => '15527210477',
        ]);
        $this->insert('{{%user}}', [
            'id' => 2,
            'username' => 'manager',
            'email' => 'manager@example.com',
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('manager'),
            'name' => 'Rose',
            'mobile' => '15527766232',
        ]);
        $this->insert('{{%user}}', [
            'id' => 3,
            'username' => 'user',
            'email' => 'user@example.com',
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('user'),
            'name' => 'Tom',
            'mobile' => '13871407676',
        ]);

        $this->insert('{{%user_profile}}', [
            'user_id' => 1,
            'locale' => Yii::$app->language,
            'firstname' => '',
            'lastname' => ''
        ]);
        $this->insert('{{%user_profile}}', [
            'user_id' => 2,
            'firstname' => '',
            'locale' => Yii::$app->language,
        ]);
        $this->insert('{{%user_profile}}', [
            'user_id' => 3,
            'firstname' => '',
            'locale' => Yii::$app->language,
        ]);

        // 创建角色和权限
        $this->createRolePermissions();

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%access_log}}")->execute();
        $this->createTable('{{%access_log}}', [
            'id' => $this->primaryKey(),
            'category' => $this->integer()->notNull()->defaultValue(1),
            'user_id' => $this->integer()->notNull()->defaultValue(0)->comment('操作者'),
            'username' => $this->string(64)->comment('账号'),
            'ip' => $this->string(32)->comment('IP'),
            'detail' => $this->text()->comment('操作内容'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%access_log}}', '操作日志');
        $this->createIndex('category', '{{%access_log}}', ['category']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%member}}")->execute();
        $this->createTable('{{%member}}', [
            'id' => $this->integer()->notNull()->defaultValue(0)->comment('用户ID'),
            'sid' => $this->string(64)->comment('加密ID')->unique(),
            'username' => $this->string(32)->comment('账号'),
            'mobile' => $this->string(16)->comment('手机')->unique(),
            'name' => $this->string(32)->comment('真实姓名'),
            //'contact' => $this->string(128)->comment('联系方式'),
            'nickname' => $this->string(64)->notNull()->defaultValue('')->comment('昵称'),
            'auth_key' => $this->string(32)->notNull()->defaultValue('')->comment('Auth密钥'),
            'access_token' => $this->string(64)->notNull()->defaultValue('')->comment('Token'),
            'password_plain' => $this->string(32)->comment('密码'),
            'password_hash' => $this->string(64)->comment('密码'),
            'email' => $this->string(32),
            'status' => $this->smallInteger()->notNull()->defaultValue(0), // 状态 0:active, 1:disable
            'gender' => $this->string(4)->notNull()->defaultValue('m')->comment('性别'), // f, m
            'area_parent_id' => $this->string(16)->notNull()->defaultValue('')->comment('省份'),
            'area_id' => $this->string(16)->notNull()->defaultValue('')->comment('城市'),
            'age' => $this->integer()->notNull()->defaultValue(0)->comment('年龄'),
            'avatar_path' => $this->string()->comment('头像'),
            'avatar_base_url' => $this->string()->comment('头像'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
            'logged_at' => $this->timestamp()->defaultValue(null)->comment('最近登录'),
            'pid' => $this->integer()->notNull()->defaultValue(0)->comment('上级ID'),
            'is_seller' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('是卖家'),
            'balance_revenue' => $this->decimal(12, 2)->notNull()->defaultValue(0)->comment('账户余额'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%member}}', '会员');
        $this->addPrimaryKey('id', '{{%member}}', ['id']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%member_profile}}")->execute();
        $this->createTable('{{%member_profile}}', [
            'id' => $this->primaryKey(),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('用户ID')->unique(),
            'is_real_name' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('已实名'),
            'identity' => $this->string(20)->comment('身份证'),
            'card_id' => $this->string(64)->notNull()->defaultValue('')->comment('银行卡号'),
            'card_name' => $this->string(64)->notNull()->defaultValue('')->comment('户名'),
            'card_branch' => $this->string(64)->notNull()->defaultValue('')->comment('支行'),
            'card_bank' => $this->string(64)->notNull()->defaultValue('')->comment('开户行'),
            'alipay_id' => $this->string(64)->notNull()->defaultValue('')->comment('支付宝账号'),
            'alipay_name' => $this->string(64)->notNull()->defaultValue('')->comment('支付宝账号姓名'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
            'ext' => $this->text()->comment('扩展信息'), // json
        ], $tableOptions);
        $this->addCommentOnTable('{{%member_profile}}', '会员');

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%member_address}}")->execute();
        $this->createTable('{{%member_address}}', [
            'id' => $this->primaryKey(),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('会员ID'),
            'name' => $this->string(64)->notNull()->defaultValue('')->comment('姓名'),
            'mobile' => $this->string(64)->notNull()->defaultValue('')->comment('联系电话'),
            'area_parent_id' => $this->string(16)->notNull()->defaultValue('')->comment('省份'),
            'area_id' => $this->string(16)->notNull()->defaultValue('')->comment('地市'),
            'district_id' => $this->string(16)->notNull()->defaultValue('')->comment('区县'),
            'address' => $this->string()->notNull()->defaultValue('')->comment('收货地址'),
            'is_default' => $this->tinyInteger()->defaultValue(0)->comment('默认'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%member_address}}', '收货地址');
        $this->createIndex('member_id', '{{%member_address}}', ['member_id']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%wishlist}}")->execute();
        $this->createTable('{{%wishlist}}', [
            'id' => $this->primaryKey(),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('收藏者'),
            'product_id' => $this->string(64)->notNull()->defaultValue('')->comment('商品ID'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%wishlist}}', '收藏商品'); // 即橱窗商品
        $this->createIndex('member_id_product_id', '{{%wishlist}}', ['member_id', 'product_id'], true);
        $this->createIndex('product_id', '{{%wishlist}}', ['product_id']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%shop}}")->execute();
        $this->createTable('{{%shop}}', [
            'id' => $this->string(64)->notNull()->defaultValue('')->comment('ID'),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('卖家ID'),
            'parent_cat' => $this->integer()->notNull()->defaultValue(0)->comment('一级分类'),
            'cat' => $this->integer()->notNull()->defaultValue(0)->comment('主营业务'),
            'title' => $this->string(32)->notNull()->defaultValue('')->comment('店铺名称'),
            'company' => $this->string(64)->notNull()->defaultValue('')->comment('公司名称'),
            'credit_code' => $this->string(64)->notNull()->defaultValue('')->comment('信用代码'),
            'area_parent_id' => $this->string(16)->notNull()->defaultValue('')->comment('省份'),
            'area_id' => $this->string(16)->notNull()->defaultValue('')->comment('城市'),
            'district_id' => $this->string(16)->notNull()->defaultValue('')->comment('区县'),
            'address' => $this->string(128)->notNull()->defaultValue('')->comment('详细地址'),
            'open_time' => $this->string(64)->notNull()->defaultValue('')->comment('营业时间'),
            'tel' => $this->string(64)->notNull()->defaultValue('')->comment('营业电话'),
            'detail' => $this->text()->comment('店铺详情'),
            'legal_person' => $this->string(16)->notNull()->defaultValue('')->comment('法人姓名'),
            'legal_identity' => $this->string(20)->notNull()->defaultValue('')->comment('法人身份证号'),
            'business_licence_image' => $this->string()->notNull()->defaultValue('')->comment('营业执照'),
            'identity_face_image' => $this->string()->notNull()->defaultValue('')->comment('法人身份证正面'),
            'identity_back_image' => $this->string()->notNull()->defaultValue('')->comment('法人身份证反面'),
            'logo_path' => $this->string()->comment('门店LOGO'),
            'logo_base_url' => $this->string()->comment('门店LOGO'),
            'seller_status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('店铺状态'), //0: 待审核, 1:已通过, 2:拒绝
            'seller_reason' => $this->string(255)->notNull()->defaultValue('')->comment('拒绝理由'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
            'seller_time' => $this->timestamp()->defaultValue(null)->comment('店铺认证时间'),
            'lat' => $this->decimal(16, 6)->comment('纬度'),
            'lon' => $this->decimal(16, 6)->comment('经度'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%shop}}', '店铺');
        $this->addPrimaryKey('id', '{{%shop}}', ['id']);
        $this->createIndex('member_id', '{{%shop}}', ['member_id'], true);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%banner}}")->execute();
        $this->createTable('{{%banner}}', [
            'id' => $this->primaryKey(),
            'cat' => $this->integer()->notNull()->defaultValue(0)->comment('类型'), // 0: 首页轮播, 1: 分类页
            'title' => $this->string()->comment('标题'),
            'detail' => $this->text()->comment('说明'),
            'img_id' => $this->integer()->notNull()->defaultValue(0)->comment('图片'), // 图片
            'img_url' => $this->string(512)->comment('图片'), // 优先
            'jump_type' => $this->integer()->notNull()->defaultValue(0)->comment('跳转类型'), // 0:URL, 1:无链接, 2: APP原生页面
            'url' => $this->string()->notNull()->defaultValue('')->comment('URL'),
            'app_function_id' => $this->integer()->notNull()->defaultValue(0)->comment('APP功能ID'),
            'second' => $this->integer()->notNull()->defaultValue(0)->comment('停留时间'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),// 0: 否 1:禁用
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%banner}}', 'Banner配置');
        $this->createIndex('sort_order', '{{%banner}}', ['cat', 'sort_order']);
        $this->createIndex('created_at', '{{%banner}}', ['cat', 'created_at']);

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%picture}}")->execute();
        $this->createTable('{{%picture}}', [
            'id' => $this->primaryKey(),
            'global_sid' => $this->string(64),
            'global_iid' => $this->integer()->notNull()->defaultValue(0)->comment('ID'),
            'path' => $this->string()->comment('文件名'),
            'base_url' => $this->string()->comment('url路径'),
            'type' => $this->string(256)->comment('类型'),
            'size' => $this->integer()->notNull()->defaultValue(0)->comment('尺寸'),
            'name' => $this->string(512)->comment('原文件名'),
            'order' => $this->integer()->comment('排序'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%picture}}', '资料库');
        $this->createIndex('global_sid', '{{%picture}}', ['global_sid']);
        $this->createIndex('global_iid', '{{%picture}}', ['global_iid']);

        Yii::$app->ks->set('order.max_wait_pay_minutes', 120);
        Yii::$app->ks->set('order.max_wait_confirm_receive', 15);
        Yii::$app->ks->set('order.max_wait_complain', 7);
        Yii::$app->ks->set('order.max_wait_rate', 7);

        if (Console::confirm('创建DEMO数据?', true)) {
            $this->seed();
        }
        return false;
    }

    public function down()
    {
        $faker = \Faker\Factory::create('zh_CN');
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
        }

        if (Yii::$app->db->schema->getTableSchema('{{%user}}') !== null) {
            $this->dropTable('{{%user_profile}}');
            $this->dropTable('{{%user}}');
            $this->dropTable('{{%access_log}}');
        }
        return true;
    }

    public function createRolePermissions()
    {
        $auth = \Yii::$app->get('authManager');
        $auth->removeAll();

        $user = $auth->createRole(User::ROLE_USER);
        $auth->add($user);

        $manager = $auth->createRole(User::ROLE_MANAGER);
        $auth->add($manager);

        $admin = $auth->createRole(User::ROLE_ADMINISTRATOR);
        $auth->add($admin);

        $permission = $auth->createPermission('内容模块');
        //$permission->description = '文章管理';
        $auth->add($permission);

        $permission = $auth->createPermission('参数设置模块');
        //$permission->description = '网站参数设置';
        $auth->add($permission);

        $permission = $auth->createPermission('后台用户模块');
        //$permission->description = '后台用户列表';
        $auth->add($permission);

        $permission = $auth->createPermission('日志模块');
        //$permission->description = '后台用户操作日志';
        $auth->add($permission);

        $permission = $auth->createPermission('角色权限模块');
        //$permission->description = '角色权限管理';
        $auth->add($permission);

        // added
        $auth->add($auth->createPermission('订单列表'));
        $auth->add($auth->createPermission('订单审核'));
        $auth->add($auth->createPermission('用户列表'));
        $auth->add($auth->createPermission('用户审核'));
        $auth->add($auth->createPermission('消息中心'));
        $auth->add($auth->createPermission('报表管理'));
        // end

        // 定义 ROLE_ADMINISTRATOR 有哪些权限
        // 1. 可以采用角色继承形式定义
        $auth->addChild($auth->getRole(\common\models\User::ROLE_ADMINISTRATOR), $auth->getRole(User::ROLE_MANAGER)); // ROLE_ADMINISTRATOR 有 ROLE_MANAGER 的所有权限
        // 2. 也可以把所有权限都直接放到ROLE_ADMINISTRATOR上
        // $auth->addChild($auth->getRole(\common\models\User::ROLE_ADMINISTRATOR), $auth->getPermission('角色权限模块'));
        foreach ($auth->getPermissions() as $permission) {
            $auth->addChild($auth->getRole(\common\models\User::ROLE_ADMINISTRATOR), $permission);
        }

        // 定义 ROLE_MANAGER 有哪些权限
        $auth->addChild($auth->getRole(\common\models\User::ROLE_MANAGER), $auth->getPermission('后台用户模块'));
        $auth->addChild($auth->getRole(\common\models\User::ROLE_MANAGER), $auth->getPermission('参数设置模块'));
        $auth->addChild($auth->getRole(\common\models\User::ROLE_MANAGER), $auth->getPermission('日志模块'));
        $auth->addChild($auth->getRole(\common\models\User::ROLE_MANAGER), $auth->getRole(User::ROLE_USER)); // ROLE_MANAGER 有 ROLE_USER 的所有权限

        // 定义 ROLE_MANAGER 有哪些权限
        $auth->addChild($auth->getRole(\common\models\User::ROLE_USER), $auth->getPermission('内容模块'));

        // 为ID为1,2,3的用户分配角色
        $auth->assign($auth->getRole(User::ROLE_ADMINISTRATOR), 1);
        $auth->assign($auth->getRole(User::ROLE_MANAGER), 2);
        $auth->assign($auth->getRole(User::ROLE_USER), 3);

        echo __METHOD__ . PHP_EOL;

        return;
    }

    public function seed()
    {
        $faker = \Faker\Factory::create('zh_CN');

        ImageManager::deleteAll();
        $files = FileHelper::findFiles(Yii::getAlias('@backend/web/image-samples/product'), ['only' => ['*.jpg']]);
        foreach ($files as $fileName) {
            $sFileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $sFileName = pathinfo($fileName, PATHINFO_BASENAME);
            $model = new ImageManager();
            $model->fileName = str_replace("_", "-", strtolower($sFileName));
            //$model->fileHash = Yii::$app->getSecurity()->generateRandomString(32);
            $model->fileHash = strtolower($sFileName);
            $model->save();
            $toFileName = Yii::$app->imagemanager->mediaPath . DIRECTORY_SEPARATOR . $model->id . '_' . $model->fileHash . '.' . $sFileExtension;
            copy($fileName, $toFileName);
        }
        $imageIds = ImageManager::find()->select('id')->column();

        $files = FileHelper::findFiles(Yii::getAlias('@backend/web/image-samples/product'), ['only' => ['*.jpg']]);
        $tmpPictures = [];
        foreach ($files as $fileName) {
            $sFileName = pathinfo($fileName, PATHINFO_BASENAME);
            $tmpPictures[] = [
                'path' => $sFileName,
                'name' => $sFileName,
                'size' => filesize($fileName),
                'type' => 'image/jpeg',
                'order' => 0,
                'base_url' => Yii::getAlias('@backendUrl/image-samples/product'),
            ];
        }

        $files = FileHelper::findFiles(Yii::getAlias('@backend/web/image-samples/cloth'), ['only' => ['*.jpg']]);
        $clothPictures = [];
        foreach ($files as $fileName) {
            $sFileName = pathinfo($fileName, PATHINFO_BASENAME);
            $clothPictures[] = [
                'path' => $sFileName,
                'name' => $sFileName,
                'size' => filesize($fileName),
                'type' => 'image/jpeg',
                'order' => 0,
                'base_url' => Yii::getAlias('@backendUrl/image-samples/cloth'),
            ];
        }

        $files = FileHelper::findFiles(Yii::getAlias('@backend/web/image-samples/people'), ['only' => ['*.jpg']]);
        $tmpAvatars = [];
        foreach ($files as $fileName) {
            $sFileName = pathinfo($fileName, PATHINFO_BASENAME);
            $tmpAvatars[] = [
                'path' => $sFileName,
                'name' => $sFileName,
                'size' => filesize($fileName),
                'type' => 'image/jpeg',
                'order' => 0,
                'base_url' => Yii::getAlias('@backendUrl/image-samples/people'),
            ];
        }

        $model = new \common\models\Member();
        $model->mobile = '15527210477';
        $model->access_token = 'token-' . $model->mobile;
        $model->setAttributes([
            'id' => Member::ROOT_ID,
            'username' => 'admin',
            'name' => 'admin',
            'nickname' => 'admin',
            'is_seller' => 1,
        ], true);
        $model->setPassword('123456');
        if (!$model->save()) {
            print_r($model->errors);
            Yii::$app->end();
        }
        echo "\n insert " . get_class($model);

        $tmpProvinceIds = $provinceIds = array_column(AreaCode::getChina()->getChildren()->asArray()->all(), 'id');

        for ($i = 0; $i < 30; $i++) {
            $area_parent_id = $tmpProvinceIds[array_rand($tmpProvinceIds)];
            $tmpCityIds = array_column(AreaCode::find()->where(['parent_id' => $area_parent_id])->asArray()->all(), 'id');
            $area_id = $tmpCityIds[array_rand($tmpCityIds)];
            shuffle($tmpPictures);
            $arr = array_slice($tmpPictures, 0, rand(1, 2));
            $model = new \common\models\Member();
            $mobile = '1' . sprintf("%010d", $i); // $faker->phoneNumber
            $model->setAttributes([
                'id' => Member::generateId(),
                'access_token' => 'token-' . $mobile,
                'mobile' => $faker->phoneNumber,
                'username' => $faker->word . rand(0, 1000),
                'name' => $faker->name,
                'nickname' => $faker->word,
                //'area_parent_id' => $area_parent_id,
                //'area_id' => $area_id,
                'picture' => $tmpAvatars[array_rand($tmpAvatars)],
            ], true);
            $model->setPassword('123456');
            if ($i == 0 || $i == 1) {
                $model->pid = Member::ROOT_ID;
            }
            if (!$model->save()) {
                print_r($model->errors);
                Yii::$app->end();
            }
            echo "\n insert " . get_class($model);
        }
        $memberIds = Member::find()->select('id')->column();
        $members = Member::find()->all();

        for ($i = 0; $i < 10; $i++) {
            $model = new \common\models\Banner();
            $model->setAttributes([
                'cat' => rand(1, 2),
                'title' => $faker->catchPhrase,
                'detail' => $faker->realText(500),
                'img_id' => $imageIds[array_rand($imageIds)],
                'jump_type' => rand(1, 3),
                'url' => $faker->url,
                'app_function_id' => rand(1, 2),
                'status' => rand(0, 1),
            ], true);
            if (!$model->save()) {
                print_r($model->errors);
                Yii::$app->end();
            }
            echo "\n insert " . get_class($model);
        }

        $rows = [
            [
                'id' => 2,
                'parent_id' => 1,
                'name' => '服装',
                'is_visual' => 1,
            ],
            [
                'id' => 3,
                'parent_id' => 1,
                'name' => '手机数码',
                'is_visual' => 1,
            ],
            [
                'id' => 4,
                'parent_id' => 1,
                'name' => '家居百货',
                'is_visual' => 1,
            ],
            [
                'id' => 5,
                'parent_id' => 1,
                'name' => '餐厨',
                'is_visual' => 1,
            ],
            [
                'id' => 6,
                'parent_id' => 1,
                'name' => '美妆护肤',
                'is_visual' => 0,
            ],
            [
                'id' => 7,
                'parent_id' => 1,
                'name' => '母婴玩具',
                'is_visual' => 1,
            ],
            [
                'id' => 8,
                'parent_id' => 1,
                'name' => '蔬果生鲜',
                'is_visual' => 0,
            ],

            [
                'id' => 20,
                'parent_id' => 2,
                'name' => '男装',
                'is_visual' => 1,
            ],
            [
                'id' => 21,
                'parent_id' => 2,
                'name' => '女装',
                'is_visual' => 1,
            ],
            [
                'id' => 22,
                'parent_id' => 2,
                'name' => '童装',
                'is_visual' => 1,
            ],
            [
                'id' => 23,
                'parent_id' => 2,
                'name' => '内衣',
                'is_visual' => 1,
            ],
            [
                'id' => 30,
                'parent_id' => 3,
                'name' => '手机通讯',
                'is_visual' => 1,
            ],
            [
                'id' => 31,
                'parent_id' => 3,
                'name' => '运营商',
                'is_visual' => 1,
            ],
            [
                'id' => 33,
                'parent_id' => 3,
                'name' => '手机配件',
                'is_visual' => 1,
            ],
            [
                'id' => 34,
                'parent_id' => 3,
                'name' => '摄影摄像',
                'is_visual' => 1,
            ],

            [
                'id' => 200,
                'parent_id' => 20,
                'name' => 'T恤',
                'is_visual' => 1,
            ],
            [
                'id' => 201,
                'parent_id' => 20,
                'name' => '牛仔裤',
                'is_visual' => 1,
            ],
            [
                'id' => 202,
                'parent_id' => 20,
                'name' => '休闲裤',
                'is_visual' => 1,
            ],
            [
                'id' => 203,
                'parent_id' => 20,
                'name' => '衬衫',
                'is_visual' => 1,
            ],
            [
                'id' => 300,
                'parent_id' => 30,
                'name' => '手机',
                'is_visual' => 1,
            ],
            [
                'id' => 301,
                'parent_id' => 30,
                'name' => 'iPAD',
                'is_visual' => 1,
            ],
        ];
        foreach ($rows as $row) {
            $parent = Category::findOne(['id' => $row['parent_id']]);
            $model = new Category();
            $model->setAttributes($row);
            $model->appendTo($parent);
            $model->save();
        }

        for ($i = 1; $i <= 30; $i++) {
            $parent = Category::find()->where(['<=', 'depth', 2])->orderBy(new Expression('rand()'))->limit(1)->one();
            $model = new \common\models\Category();
            $model->setAttributes([
                'name' => $faker->word,
            ], true);
            $model->appendTo($parent);
            $model->parent_id = $parent->id;
            if (!$model->save()) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                echo 'prependTo error';
                Yii::$app->end();
            }
            echo "\n insert " . get_class($model);
        }

        $arr1 = ['餐饮', '生活服务', '休闲娱乐', '购物', '酒店住宿'];
        for ($i = 0; $i < 5; $i++) {
            $model = new \common\models\ShopCategory();
            $model->setAttributes([
                'name' => $arr1[$i],
            ], true);
            if (!$model->save()) {
                print_r($model->errors);
                Yii::$app->end();
            }
            echo "\n insert " . get_class($model);

            $c = rand(2, 5);
            for ($j = 0; $j < $c; $j++) {
                $child = new \common\models\ShopCategory();
                $child->setAttributes([
                    'name' => $faker->word,
                    'parent_id' => $model->id,
                ], true);
                if (!$child->save()) {
                    print_r($child->errors);
                    Yii::$app->end();
                }

                echo "\n insert child" . get_class($child);
            }
        }

        for ($i = 1; $i <= 10; $i++) {
            $member = Member::find()->orderBy(new Expression('rand()'))->limit(1)->one();
            $model = Shop::findOne(['member_id' => $member->id]);
            if ($model !== null) {
                continue;
            }
            $area_parent_id = $tmpProvinceIds[array_rand($tmpProvinceIds)];
            $tmpCityIds = array_column(AreaCode::find()->where(['parent_id' => $area_parent_id])->asArray()->all(), 'id');
            $area_id = $tmpCityIds[array_rand($tmpCityIds)];
            $tmpDistrictIds = array_column(AreaCode::find()->where(['parent_id' => $area_id])->asArray()->all(), 'id');
            $district_id = empty($tmpDistrictIds) ? '' : $tmpDistrictIds[array_rand($tmpDistrictIds)];
            $category = ShopCategory::find()->where('parent_id > 0')->orderBy(new Expression('rand()'))->limit(1)->one();
            $model = new \common\models\Shop();
            $model->setAttributes([
                'member_id' => $member->id,
                'title' => $faker->catchPhrase,
                'parent_cat' => $category->parent_id,
                'cat' => $category->id,
                'company' => $faker->company,
                'credit_code' => $faker->creditCardNumber(),
                'area_parent_id' => $area_parent_id,
                'area_id' => $area_id,
                'district_id' => $district_id,
                'address' => $faker->address,
                'tel' => $faker->phoneNumber,
                'detail' => $faker->realText(20),
                'legal_person' => $faker->name(),
                'legal_identity' => $faker->phoneNumber,
                'open_time' => '',
                'seller_status' => rand(0, 2),
                'status' => rand(0, 1),
            ], true);
            if (!$model->save(false)) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                Yii::$app->end();
            }
            echo "\n insert " . get_class($model);
        }


        $rows = [
            [
                'id' => 1,
                'name' => '颜色',
                'sort_order' => rand(0, 0),
            ],
            [
                'id' => 2,
                'name' => '尺码',
                'sort_order' => rand(0, 0),
            ],
            [
                'id' => 3,
                'name' => '款式',
                'sort_order' => rand(0, 0),
            ],
            [
                'id' => 4,
                'name' => '内存',
                'sort_order' => rand(0, 0),
            ],
        ];
        foreach ($rows as $row) {
            $model = new \common\models\Option();
            $model->setAttributes($row);
            echo "\n insert " . $model->className() . ($model->save() ? 'ok' : 'error ' . print_r($model->errors, true)) . "\n";
        }

        $rows = [
            [
                'option_id' => 1,
                'name' => '红色',
                'sort_order' => rand(0, 0),
            ],
            [
                'option_id' => 1,
                'name' => '黄色',
                'sort_order' => rand(0, 0),
            ],
            [
                'option_id' => 1,
                'name' => '白色',
                'sort_order' => rand(0, 0),
            ],
            [
                'option_id' => 2,
                'name' => 'S',
                'sort_order' => rand(0, 0),
            ],
            [
                'option_id' => 2,
                'name' => 'L',
                'sort_order' => rand(0, 0),
            ],
            [
                'option_id' => 3,
                'name' => '韩版',
                'sort_order' => rand(0, 0),
            ],
            [
                'option_id' => 3,
                'name' => '欧版',
                'sort_order' => rand(0, 0),
            ],
            [
                'option_id' => 4,
                'name' => '4G内存',
                'sort_order' => rand(0, 0),
            ],
            [
                'option_id' => 4,
                'name' => '8G内存',
                'sort_order' => rand(0, 0),
            ],
        ];
        foreach ($rows as $row) {
            $model = new \common\models\OptionValue();
            $model->setAttributes($row);
            echo "\n insert " . $model->className() . ($model->save() ? 'ok' : 'error ' . print_r($model->errors, true)) . "\n";
        }

        $rows = [
            [
                'category_id' => 200,
                'option_id' => '1',
            ],
            [
                'category_id' => 201,
                'option_id' => '1',
            ],
            [
                'category_id' => 202,
                'option_id' => '1',
            ],
            [
                'category_id' => 203,
                'option_id' => '1',
            ],
            [
                'category_id' => 200,
                'option_id' => '2',
            ],
            [
                'category_id' => 201,
                'option_id' => '2',
            ],
            [
                'category_id' => 202,
                'option_id' => '2',
            ],
            [
                'category_id' => 203,
                'option_id' => '2',
            ],
            [
                'category_id' => 300,
                'option_id' => '4',
            ],
            [
                'category_id' => 301,
                'option_id' => '4',
            ],

        ];
        foreach ($rows as $row) {
            $model = new \common\models\CategoryOption();
            $model->setAttributes($row);
            echo "\n insert " . $model->className() . ($model->save() ? 'ok' : 'error ' . print_r($model->errors, true)) . "\n";
        }
        $memberIds = Member::find()->select('id')->column();

        for ($i = 1; $i <= 50; $i++) {
            $category = Category::find()->orderBy(new Expression('rand()'))->limit(1)->one(); // where(['depth' => 3])->
            if (!$category->isLeaf()) {
                continue;
            }
            shuffle($clothPictures);
            $arr = array_slice($clothPictures, 0, rand(1, 2));
            shuffle($clothPictures);
            $detailArr = array_slice($clothPictures, 0, rand(1, 3));
            $parents = $category->getParentsNode(true, false);
            $member_id = Util::haveProbability(600000) ? Member::ROOT_ID : $memberIds[array_rand($memberIds)];
            $model = new \common\models\Product();
            $model->setAttributes([
                'member_id' => $member_id,
                'category_id1' => ArrayHelper::getValue($parents, '0.id', 0),
                'category_id2' => ArrayHelper::getValue($parents, '1.id', 0),
                'category_id3' => ArrayHelper::getValue($parents, '2.id', 0),
                'title' => $faker->catchPhrase,
                'price' => rand(100, 300),
                'cost_price' => rand(10, 99),
                'market_price' => rand(300, 500),
                'status_listing' => rand(0, 2),
                'product_pictures' => $arr,
                'detail_pictures' => $detailArr,
            ], true);
            if (!$model->save()) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                echo "\n insert " . $model->className() . print_r($model->errors, true) . "\n";
                Yii::$app->end();
            }
            echo "\n insert " . get_class($model);

            $model->initCategoryOption();
        }

        $models = Product::find()->all();
        foreach ($models as $model) {
            $model->initSku();
        }

        $models = Sku::find()->all();
        foreach ($models as $model) {
            $model->quantity = 100;
            $model->save();
        }

        for ($i = 1; $i <= 30; $i++) {
            $buyer = Member::find()->orderBy(new Expression('rand()'))->limit(1)->one();
            $sku = Sku::find()->orderBy(new Expression('rand()'))->limit(1)->one();
            $model = new \common\models\Cart();
            $model->setAttributes([
                'buyer_id' => $buyer->id,
                'product_id' => $sku->product_id,
                'sku_id' => $sku->id,
                'sku_code' => $sku->sku_code,
                'quantity' => rand(1, 5),
            ], true);
            if (!$model->save()) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                echo print_r($model->errors, true) . "\n";
                Yii::$app->end();
            }
            echo "\n insert " . get_class($model);
        }

        for ($i = 1; $i <= 30; $i++) {
            $seller = Member::find()->orderBy(new Expression('rand()'))->limit(1)->one();
            $buyer = Member::find()->orderBy(new Expression('rand()'))->limit(1)->one();
            $memberAddress = MemberAddress::find()->orderBy(new Expression('rand()'))->andWhere(['member_id' => $buyer->id])->limit(1)->one();
            $order = new \common\models\Order();
            $order->setAttributes([
                'member_id' => Util::haveProbability(600000) ? Member::ROOT_ID : $seller->id,
                'buyer_id' => $buyer->id,
                'mobile' => $buyer->mobile,
                'nickname' => $buyer->nickname,
                'quantity' => 0,
                'pay_amount' => rand(10, 200),
                'pay_method' => rand(0, 2),
                'express_company' => $faker->company,
                'express_code' => $faker->creditCardNumber,
                'status' => rand(1, 6),
                'shipping_area_parent_id' => empty($memberAddress->area_parent_id) ? '' : $memberAddress->area_parent_id,
                'shipping_area_id' => empty($memberAddress->area_id) ? '' : $memberAddress->area_id,
                'district_id' => empty($memberAddress->district_id) ? '' : $memberAddress->district_id,
                'address' => empty($memberAddress->address) ? '' : $memberAddress->address,
            ], true);
            if (!$order->save()) {
                Yii::error([__METHOD__, __LINE__, $order->errors]);
                echo print_r($order->errors, true) . "\n";
                Yii::$app->end();
            }
            echo "\n insert " . get_class($order);

            // insert order sku
            $total_amount = $quantity = 0;
            for ($j = 1; $j <= 3; $j++) {
                $sku = Sku::find()->orderBy(new Expression('rand()'))->limit(1)->one();
                $ar = new \common\models\OrderSku();
                $ar->setAttributes([
                    'order_id' => $order->id,
                    'member_id' => $order->member_id,
                    'buyer_id' => $order->buyer_id,
                    'sku_id' => $sku->id,
                    'sku_code' => $sku->sku_code,
                    'price' => $price = rand(1, 50),
                    'quantity' => $qty = rand(1, 3),
                ], true);
                if (!$ar->save()) {
                    Yii::error([__METHOD__, __LINE__, $ar->errors]);
                    echo print_r($ar->errors, true) . "\n";
                    Yii::$app->end();
                }
                echo "\n insert " . get_class($ar);

                $total_amount += $ar->amount;
                $quantity += $ar->quantity;
            }

            $order->updateAttributes([
                'total_amount' => $total_amount,
                'quantity' => $quantity,
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $orderSku = OrderSku::find()->orderBy(new Expression('rand()'))->limit(1)->one();
            if (OrderSkuRefund::findOne(['order_sku_id' => $orderSku->id])) {
                continue;
            }
            $rand_ship = rand(0, 1);
            $order = $orderSku->order;
            $model = new OrderSkuRefund();
            $model->setAttributes([
                'order_sku_id' => $orderSku->id,
                'need_ship' => rand(0, 1),
                'status' => rand(1, 4),
                'order_id' => $orderSku->order_id,
                'product_id' => $orderSku->product_id,
                'buyer_id' => $orderSku->buyer_id,
                'member_id' => $orderSku->member_id,
                'refund_amount' => $orderSku->amount,
                'mobile' => ArrayHelper::getValue($order, 'buyer.mobile'),
                'nickname' => ArrayHelper::getValue($order, 'buyer.nickname'),
                'refund_reason' => $faker->catchPhrase,
            ], true);
            if (!$model->save()) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                echo print_r($model->errors, true) . "\n";
                Yii::$app->end();
            }
            echo "\n insert " . get_class($model);
        }

        for ($i = 1; $i <= 11; $i++) {
            $orderSku = OrderSku::find()->orderBy(new Expression('rand()'))->limit(1)->one();
            shuffle($tmpPictures);
            $arr = array_slice($tmpPictures, 0, rand(1, 2));
            if (Rate::findOne(['order_sku_id' => $orderSku->id])) {
                continue;
            }
            $model = new Rate();
            $model->setAttributes([
                'order_sku_id' => $orderSku->id,
                'content' => $faker->realText(100),
                'score' => rand(1, 3),
                'ip' => $faker->ipv4,
                'is_anonymous' => rand(0, 1),
                'is_hidden' => rand(0, 1),
                'rate_pictures' => $arr,
            ], true);
            if (!$model->save()) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                echo print_r($model->errors, true) . "\n";
                Yii::$app->end();
            }
            echo "\n insert " . get_class($model);
        }

        for ($i = 1; $i <= 11; $i++) {
            $buyer = Member::find()->orderBy(new Expression('rand()'))->limit(1)->one();
            $product = Product::find()->orderBy(new Expression('rand()'))->limit(1)->one();
            shuffle($tmpPictures);
            $arr = array_slice($tmpPictures, 0, rand(1, 2));
            $model = new Rate();
            $model->setAttributes([
                //'order_id' => 'xx',
                'product_id' => $product->id,
                //'product_title' => $product->title,
                'buyer_id' => $buyer->id,
                //'nickname' => $buyer->nickname,
                'content' => $faker->realText(100),
                'score' => rand(1, 3),
                'ip' => $faker->ipv4,
                'is_anonymous' => rand(0, 1),
                'is_hidden' => rand(0, 1),
                'rate_pictures' => $arr,
            ], true);
            if (!$model->save()) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
                echo print_r($model->errors, true) . "\n";
                Yii::$app->end();
            }
            echo "\n insert " . get_class($model);
        }

        for ($i = 0; $i < 20; $i++) {
            $member_id = $memberIds[array_rand($memberIds)];
            $product = Product::find()->orderBy(new Expression('rand()'))->limit(1)->one();
            if (Wishlist::findOne(['member_id' => $member_id, 'product_id' => $product->id])) {
                continue;
            }
            $model = new Wishlist();
            $model->setAttributes([
                'member_id' => $member_id,
                'product_id' => $product->id,
            ]);
            if (!$model->save()) {
                print_r($model->errors);
                Yii::$app->end();
            }
            echo "\n insert " . get_class($model);
        }

        for ($i = 0; $i < 30; $i++) {
            $model = new \common\models\MemberAddress();
            $member = Member::find()->orderBy(new Expression('rand()'))->limit(1)->one();
            $area_parent_id = $tmpProvinceIds[array_rand($tmpProvinceIds)];
            $tmpCityIds = array_column(AreaCode::find()->where(['parent_id' => $area_parent_id])->asArray()->all(), 'id');
            $area_id = $tmpCityIds[array_rand($tmpCityIds)];
            $tmpDistrictIds = array_column(AreaCode::find()->where(['parent_id' => $area_id])->asArray()->all(), 'id');
            $district_id = empty($tmpDistrictIds) ? '' : $tmpDistrictIds[array_rand($tmpDistrictIds)];
            $model->setAttributes([
                'member_id' => $member->id,
                'name' => $faker->name,
                'mobile' => $faker->phoneNumber,
                'address' => $faker->address,
                'area_parent_id' => $area_parent_id,
                'area_id' => $area_id,
                'district_id' => $district_id,
            ], true);
            if (!$model->save()) {
                print_r($model->errors);
                Yii::$app->end();
            }
            echo "\n insert " . get_class($model);
        }

    }

}

