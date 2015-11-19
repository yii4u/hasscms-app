<?php
/**
 *
* HassCMS (http://www.hassium.org/)
*
* @link http://github.com/hasscms for the canonical source repository
* @copyright Copyright (c) 2016-2099 Hassium Software LLC.
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
namespace hass\plugin\controllers;


use hass\backend\BaseController;

use yii\data\ArrayDataProvider;
use hass\backend\enums\BooleanEnum;
use yii\helpers\FileHelper;
use hass\helpers\Util;

/**
*
* @package hass\package_name
* @author zhepama <zhepama@gmail.com>
* @since 0.1.0
 */
class DefaultController extends BaseController
{

    /**
     * Lists all Module models.
     * @return mixed
     */
    public function actionIndex()
    {
        $packages =  Util::getPluginLoader()->findAll();
        $dataProvider = new ArrayDataProvider(["allModels"=>$packages,"key"=>function ($model){return $model->package;}]);
        return $this->render('index',['dataProvider'=>$dataProvider]);
    }

    /**
     * 切换插件
     * @param unknown $id
     * @param unknown $value
     */
    public function actionSwitcher($id, $value)
    {
        $plugin = Util::getPluginLoader()->findOne($id);
        $model = $plugin->getModel();
        $model->setAttribute("status", $value);
        $model->save();
        return $this->renderJsonMessage(true,\Yii::t("hass", "更新成功"));
    }

    /**
     * 删除插件
     * @param unknown $id
     */
    public function actionDelete($id)
    {
        $plugin = Util::getPluginLoader()->findOne($id);
        if($plugin != null)
        {
            $plugin->uninstall();
            $model = $plugin->getModel();
            $model->delete();

            $plugin->deletePackage();
        }

        $this->flash("success", "删除插件成功");
        return $this->redirect([
            "index"
        ]);
    }

    /**
     * 安装插件
     * @param unknown $id
     */
    public function actionInstall($id) {
        $plugin = Util::getPluginLoader()->findOne($id);
        $result =  $plugin->install();
        if($result == true)
        {
            $model = $plugin->getModel();
            $model->setAttribute("installed", BooleanEnum::TRUE);
            $model->save();

            $this->flash("success", "安装成功");
        }
        else
        {
            $this->flash("error", "安装失败");
        }

        return $this->redirect(["index"]);
    }

    /**
     * 卸载插件
     * @param unknown $id
     */
    public function actionUninstall($id)
    {
        $plugin = Util::getPluginLoader()->findOne($id);
        $result =  $plugin->uninstall();
        if($result == true)
        {
            $model = $plugin->getModel();
            $model->delete();
            $this->flash("success", "卸载成功");
        }
        else
        {
            $this->flash("error", "卸载失败");
        }

        return $this->redirect(["index"]);
    }

}