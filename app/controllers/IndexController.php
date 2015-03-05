<?php
/**
 * Created by PhpStorm.
 * User: xuyi
 * Date: 15/3/2
 * Time: 下午4:35
 */

//todo intern need to be changed
namespace app\controllers;

//加载组件和models
use app\components\Controller;//控制器
use app\components\Pagination;//分页
use app\models\Intern;//产品
use app\models\Red_Heart;//收藏
use app\models\Resume;
//加载应用
use framework\web\App;

class IndexController extends Controller
{
    const INTERNS_LIMIT = 5;

    public function indexAction()
    {
        $this->layout = "header_icon";
        $this->render('index');
    }

    public function internsAction()
    {
        $this->layout = "rooter_back";
        $this->html_title = "Demon-demon-demon";
        $this->navigation_title = "DEMON";
        //获得关键词
        if (isset($_GET['keyword'])) {
            $keyword = $_GET['keyword'];
        } else {
            $keyword = null;
        }
        //获得城市
        if (isset($_GET['city'])) {
            $city = $_GET['city'];
        } else {
            $city = null;
        }
        $intern_model = new Intern();
        $count = $intern_model->getInternCount($keyword, $city);
        $page = new Pagination($count);
        $page->pageSize = self::INTERNS_LIMIT;
        $intern_list = $intern_model->getInernList($keyword, $page->offset, $page->limit, $city);
        $this->render('interns', array('$intern_list' => $intern_list, 'page' => $page));
    }


    public function internAction()
    {
        $this->layout = 'rooter_back';
        //获取详细信息
        $intern_id = $_GET['intern_id'];
        //检验Id 如果错误跳转Error页面
        if (empty($intern_id) || !is_numeric($intern_id)) {
            $this->redirect('site/userError');
        }
        $intern_model = new Intern();
        $intern = $intern_model->getInternDetail($intern_id);
        if(!$intern){
            $this->redirect('site/user');
        }
        //初始化页面
        $this->html_title = $intern['name'].'|'.$intern['com_name'].'|'."xuyi's Demon";
        $this->navigation_title = $intern['name'];
        //标记当前用户和当前产品之间的关系 //表结构还没建立,待定
        $resumes = null;
        if(!App::$user->getIsLogin() ){
            $user_intern_status = 1;
            $collect_status = 1;
            $sso_back_url_data = array();
            $sso_back_url_data[0] = 'index/intern';
            $sso_back_url_data[1] = array();
            $sso_back_url_data[1]['intern_id'] = $intern_id;
            setcookie("sso_back_url",json_encode($sso_back_url_data),0,'/');
        }
        else {
            $user_intern_status = $intern_model->getUserIntermStatus(App::$user->getId(),$intern_id);
            if ($user_intern_status == 2) {
                $resume_model = new Resume();
                $resumes = $resume_model->getResumeBasicInfoList(App::$user->getId());
            }
            $red_heart = new Red_Heart();
            $collect_status = $red_heart->isCollect(App::$user->getId(), 1, $intern_id)?3:2;//1是查询产品
        }
        $this->render('intern',array('intern'=>$intern,'user_intern_status'=>$user_intern_status, 'collect_status'=>$collect_status, 'resumes'=>$resumes));
    }

    public function internsAttAction(){
        //针对ajax的请求的集合action
        //还未编写lol

    }

    public function testAction(){
        $this->layout = 'test';
        $this->navigation_title = 'TEST';
        $this->render('test');
    }
}

