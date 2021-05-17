<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Bilibili追番表。从<a href="https://www.wikimoe.com/?post=136" target="_blank">WikimoeBangumi</a>派生，感谢大佬<a href="http://www.wikimoe.com" target="_blank">@广树</a>。
 *
 * @package BiliBangumi
 * @author LovesAsuna
 * @version 1.0.0
 * @link https://www.bwsl.wang
 */
class BiliBangumi_Plugin implements Typecho_Plugin_Interface {


    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate() {
        Helper::addRoute("route_BiliBangumi", "/BiliBangumi", "BiliBangumi_Action", "action");
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate() {
        Helper::removeRoute("route_BiliBangumi");
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form) {
        /**表单设置 */
        $userID = new Typecho_Widget_Helper_Form_Element_Text('userID', NULL, NULL, _t('输入B站UID'));
        $form->addInput($userID);
        $cookie = new Typecho_Widget_Helper_Form_Element_Text('cookie', NULL, NULL, _t('把cookie复制进来'), _t('如果你的追番列表是公开的，那不写这个大概也成吧…只是取不到追番进度<br><a href="https://www.bwsl.wang/csother/85.html" target="_blank">点这里查看详细使用方法</a>'));
        $form->addInput($cookie);
        $blocks = new Typecho_Widget_Helper_Form_Element_Text('pageSize', NULL, NULL, _t('每页数量'), _t('指定每页显示的番剧数量，默认16'));
        $form->addInput($blocks);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */

    /**
     * 页头输出CSS
     *
     * @access public
     * @param unknown header
     */
    public static function header() {
        $Path = Helper::options()->pluginUrl . '/BiliBangumi/';
        echo '<link rel="stylesheet" type="text/css" href="' . $Path . 'css/bangumi.css" />';
        echo "<meta name='referrer' content='never'>";
        echo "<script type='text/javascript' src='https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js'></script>";
        echo <<<script
<script type="text/javascript">
     function reload(url) {
         $.get(url, function(data) {
             $("article.post-item").html(data);
         })
     }
</script>
script;
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form) {
    }

    public static function output() {
        self::header();
        include_once "Action.php";
        $api = BilibiliAPI::getInstance();
        echo $api->get_bgm_items();
    }
}
