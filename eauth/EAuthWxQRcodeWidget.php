<?php
/**
 * User: magic
 * Date: 3/2/16 3:29 PM
 * Email: <zhongguovu@gmail.com>
 * This is not a free software! You can only be used for commercial purposes without the modification of the program code and use;
 * Program code does not allow any form of any redistribution purposes.
 */
class EAuthWxQRcodeWidget extends CWidget{
    /**
     * @var string the action to use for dialog destination. Default: the current route.
     */
    public $action = null;

    public $cssFile = true;

    //页面返回地址
    private $redirect_uri = 'http://www.bmob.cn/site/Wxcallback/service/weixin';

    private $weiXinJs = 'http://res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js';

    public function init() {
        parent::init();



        // EAuth component

    }

    /**
     * Executes the widget.
     * This method is called by {@link CBaseController::endWidget}.
     */
    public function run() {
        parent::run();

        $row = $this->Assets();

        $parameter = '{
                    id: "login_container",
                    appid: "wxbcf1efae1a3fe7d0",
                    scope: "snsapi_login",
                    redirect_uri: "'.$this->redirect_uri.'",
                    state: "",
                    style: "black",
                    href: "'.$row['css'].'"
                }';


        $this->render('QRcode', array(
            'parameter'=>$parameter
        ));
    }

    /**
     * Register CSS and JS files.
     */
    protected function Assets() {


        $assets_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
        $url = Yii::app()->assetManager->publish($assets_path, false, -1, Yii::app()->assetManager->linkAssets?false:YII_DEBUG);

        $result = array(
            'css' =>'http://'.$_SERVER['HTTP_HOST'].$url . '/css/QRcode.css',
        );

        return $result;

    }

}