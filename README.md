# yii1.1-eauth-weixin
---
# 微信新接口 微信联合登录

>
通过接入微信登录功能，用户可使用微信帐号快速登录你的网站，降低注册门槛，提高用户留存

# 依赖
* ###[微信API](https://open.weixin.qq.com)

* ###[EOAuth extension](http://www.yiiframework.com/extension/eoauth)
* ###[loid extension](http://www.yiiframework.com/extension/loid)


#操作步骤
1. ####安装Eoauth [EOAuth extension](http://www.yiiframework.com/extension/eoauth)

2. #### 修改 ```eauth/services/WeiXinOAuthService.php```
``` 
    protected $secret = '';
    protected $open_id = '';
    protected $scope = 'snsapi_login';
```
3. 登陆页面加入```入口代码```

```

<?php
...
    public function actionLogin() {
        $serviceName = Yii::app()->request->getQuery('service');
        if (isset($serviceName)) {
            /** @var $eauth EAuthServiceBase */
            $eauth = Yii::app()->eauth->getIdentity($serviceName);
            $eauth->redirectUrl = Yii::app()->user->returnUrl;
            $eauth->cancelUrl = $this->createAbsoluteUrl('site/login');

            try {
                if ($eauth->authenticate()) {
                    //var_dump($eauth->getIsAuthenticated(), $eauth->getAttributes());
                    $identity = new EAuthUserIdentity($eauth);

                    // successful authentication
                    if ($identity->authenticate()) {
                        Yii::app()->user->login($identity);
                        //var_dump($identity->id, $identity->name, Yii::app()->user->id);exit;

                        // special redirect with closing popup window
                        $eauth->redirect();
                    }
                    else {
                        // close popup window and redirect to cancelUrl
                        $eauth->cancel();
                    }
                }

                // Something went wrong, redirect to login page
                $this->redirect(array('site/login'));
            }
            catch (EAuthException $e) {
                // save authentication error to session
                Yii::app()->user->setFlash('error', 'EAuthException: '.$e->getMessage());

                // close popup window and redirect to cancelUrl
                $eauth->redirect($eauth->getCancelUrl());
            }
        }

        // default authorization code through login/password ..
    }
   ```




# 示例
###1. 前台2种方法调用
####方法一``` 跳转法，类似一号店目前效果 ```：

<code><?php
    $this->widget('ext.eauth.EAuthWidget', array('action' => 'site/login'));
?></code>
####方法二``` js调用，当前页面显示二维码 ```：
<code><?php
    $this->widget('ext.eauth.EAuthWxQRcodeWidget', array('action' => 'site/login'));
?></code>

###2. 显示效果
![图片](./makedown-img/code.png =200x290 '二维码')

