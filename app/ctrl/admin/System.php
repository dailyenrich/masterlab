<?php

namespace main\app\ctrl\admin;
use main\app\classes\UserLogic;
use main\app\ctrl\BaseAdminCtrl;
use main\app\model\system\MailQueueModel;
use main\app\model\project\ProjectRoleModel;
use main\app\model\project\ProjectModel;
use main\app\model\SettingModel;
use main\app\model\system\AnnouncementModel;
use main\app\model\user\GroupModel;
use main\app\model\PermissionGlobalModel;
use main\app\model\PermissionGlobalGroupModel;
use main\app\classes\SystemLogic;
use main\app\classes\MailQueueLogic;


/**
 * 系统控制器
 */
class System extends BaseAdminCtrl
{

    static $page_sizes = [10,20,50,100];


    public function index(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'setting';
        $data['left_nav_active'] = 'setting';
        $this->render('gitlab/admin/system_basic_setting.php' ,$data );
    }

    public function basic_setting_edit(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'setting';
        $data['left_nav_active'] = 'setting';
        $this->render('gitlab/admin/system_basic_setting_form.php' ,$data );
    }

    public function setting_fetch( $module='' )
    {
        $settingModel = new SettingModel();
        $rows = $settingModel->getSettingByModule( $module );
        if( !empty( $rows ) ){
            $json_type = ['radio','select','checkbox'];
            foreach ( $rows as &$row ){
                $_value = $row['_value'];
                $row['text'] = $_value;

                if( in_array( $row['form_input_type'] ,$json_type )  ){
                    $row['form_optional_value'] = json_decode( $row['form_optional_value'],true );
                    // 单选值显示
                    if( in_array( $row['form_input_type'] ,['radio','select'] ) ){
                        if( isset( $row['form_optional_value'] [$_value] ) ){
                            $row['text'] = $row['form_optional_value'] [$_value];
                        }
                    }
                    // 多选值显示
                    if( $row['form_input_type']=='checkbox' ){
                        $tmp = [];
                        $_value_arr = explode(',',$_value );
                        if( !empty($row['form_optional_value']) ){
                            foreach ($_value_arr as $v ){
                                if( isset( $row['form_optional_value'] [$v] ) ){
                                    $tmp[] = $row['form_optional_value'] [$v];
                                }
                            }
                        }
                        if( !empty( $tmp ) ) {
                            $row['text'] = implode(',',$tmp);
                        }
                    }
                }
            }
        }
        $datas = [];
        $datas['settings'] = $rows;
        $this->ajaxSuccess('ok',$datas );
    }

    public function basic_setting_update( $params )
    {
        if( empty($params) ){
            $this->ajaxFailed('params_is_empty');
        }
        $settingModel = new SettingModel();
        foreach( $params as $key=>$value ) {
            $settingModel->updateSetting( $key,$value );
        }
        // @todo 清除缓存
        $this->ajaxSuccess('ok');
    }

    public function upload(){

        $data = [];
        $data['error'] = 0;
        $data['url'] = 'http://192.168.3.213/uploads/user/avatar/15/avatar.png';
        echo json_encode( $data );
        die;
        $this->ajaxSuccess('ok',$data);
    }

    public function security(){

        $this->project_role();
    }


    public function project_role(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'security';
        $data['left_nav_active'] = 'project_role';
        $this->render('gitlab/admin/system_project_role.php' ,$data );
    }

    public function project_role_fetch(){

        $model = new ProjectRoleModel();
        $roles =  $model->getAll();
        $data = [];
        $data['roles'] = $roles;
        $this->ajaxSuccess('ok', $data );
    }

    public function project_role_add( $params ){

        if( empty($params) ){
            $this->ajaxFailed('params_is_empty');
        }

        if( !isset( $params['name'] ) ){
            $this->ajaxFailed('name_is_empty');
        }

        $model = new ProjectRoleModel();
        list( $ret, $last_insert_id ) = $model->add( $params );

        if( !$ret ){
            $this->ajaxFailed('server_error,'.$last_insert_id);
        }
        // @todo 清除缓存
        $this->ajaxSuccess('ok');
    }

    public function project_role_delete( $id ){

        if( empty($id) ){
            $this->ajaxFailed('params_is_empty');
        }

        $id = intval($id);

        $model = new ProjectRoleModel();
        $role = $model->getRowById( $id );
        if( !isset($role['id'] ) ){
            $this->ajaxFailed('params_is_error');
        }
        if(  $role['is_system']=='1'   ){
            $this->ajaxFailed('system_data_not_delete');
        }
        $ret  = $model->deleteById( $id );

        if( !$ret ){
            $this->ajaxFailed('server_error');
        }
        // @todo  清除关联数据 清除缓存
        $this->ajaxSuccess('ok');
    }


    public function global_permission(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'security';
        $data['left_nav_active'] = 'global_permission';
        $this->render('gitlab/admin/system_global_permission.php' ,$data );
    }

    public function global_permission_fetch(){

        $model = new PermissionGlobalModel();
        $perms =  $model->getAll();
        $permGroupModel = new PermissionGlobalGroupModel();
        $perms_groups =  $permGroupModel->getAll();

        $groupModel = new GroupModel();
        $groups =  $groupModel->getAll();
        if( !empty($perms) ){
            foreach ( $perms as &$p ){
                $has_groups = [];
                if( !empty($perms_groups) ){
                    foreach ( $perms_groups as $pg ){
                        if( $pg['perm_global_id']==$p['id'] ){
                            if( isset($groups[$pg['group_id']]) ){
                                $tmp = [];
                                $tmp = $groups[$pg['group_id']];
                                $tmp['perm_group_id'] = $pg['id'];
                                $tmp['is_system'] = $pg['is_system'];
                                $has_groups[] = $tmp;
                            }
                        }
                    }
                }
                $p['groups'] = $has_groups;
            }
        }

        $data = [];
        $data['items'] = $perms;
        $data['groups'] = array_values( $groups );
        $this->ajaxSuccess('ok', $data );
    }

    public function global_permission_group_add( $params ){

        if( empty($params) ){
            $this->ajaxFailed('params_is_empty');
        }

        if( !isset( $params['perm_id'] ) ){
            $this->ajaxFailed('perm_is_empty');
        }

        if( !isset( $params['group_id'] ) ){
            $this->ajaxFailed('group_is_empty');
        }

        $model = new PermissionGlobalGroupModel();

        // 判断是否重复
        $row = $model->getByParentIdAndGroupId( (int)$params['perm_id'],(int)$params['group_id']  );
        if( isset($row['id']) ){
            $this->ajaxFailed('perm_have_add');
        }

        list( $ret, $last_insert_id ) = $model->add( (int)$params['perm_id'],(int)$params['group_id']  );

        if( !$ret ){
            $this->ajaxFailed('server_error,'.$last_insert_id);
        }
        // @todo 清除缓存
        $this->ajaxSuccess('ok');
    }

    public function global_permission_group_delete( $id ){

        if( empty($id) ){
            $this->ajaxFailed('params_is_empty');
        }

        $id = intval($id);

        $model = new PermissionGlobalGroupModel();
        $row = $model->getRowById( $id );
        if( !isset($row['id'] ) ){
            $this->ajaxFailed('params_is_error');
        }
        $ret  = $model->deleteById( $id );

        if( !$ret ){
            $this->ajaxFailed('server_error');
        }
        // @todo  清除关联数据 清除缓存
        $this->ajaxSuccess('ok');
    }

    public function password_strategy(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'security';
        $data['left_nav_active'] = 'password_strategy';
        $this->render('gitlab/admin/system_password_strategy.php' ,$data );
    }

    public function user_session(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'security';
        $data['left_nav_active'] = 'user_session';
        $this->render('gitlab/admin/system_user_session.php' ,$data );
    }

    public function datetime_setting(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'setting';
        $data['left_nav_active'] = 'datetime_setting';
        $this->render('gitlab/admin/system_datetime_setting.php' ,$data );
    }

    public function attachment_setting(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'setting';
        $data['left_nav_active'] = 'attachment_setting';
        $this->render('gitlab/admin/system_attachment_setting.php' ,$data );
    }


    public function ui_setting(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'setting';
        $data['left_nav_active'] = 'ui_setting';
        $this->render('gitlab/admin/system_ui_setting.php' ,$data );
    }



    public function user_default_setting(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'ui';
        $data['left_nav_active'] = 'user_default_setting';
        $this->render('gitlab/admin/system_user_default_setting.php' ,$data );
    }

    public function announcement(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'ui';
        $data['left_nav_active'] = 'announcement';
        $this->render('gitlab/admin/system_announcement.php' ,$data );
    }
    public function announcement_release( $content, $expire_time ){

        if( empty($content) ){
            $this->ajaxFailed('content_is_empty');
        }
        $expire_time = intval( $expire_time );
        $model = new AnnouncementModel();
        $model->release( $content, $expire_time );

        // @todo 清除缓存
        $this->ajaxSuccess('ok');
    }

    public function announcement_disable(   ){

        $model = new  AnnouncementModel();
        $model->disable(  );

        // @todo 清除缓存
        $this->ajaxSuccess('ok');
    }



    public function smtp_config(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'email';
        $data['left_nav_active'] = 'smtp_config';
        $this->render('gitlab/admin/system_smtp_config.php' ,$data );
    }

    public function mail_test( $params=[] ){

        ob_start();
        $settingModel = new SettingModel();
        $settings =  $settingModel->getSettingByModule('mail' );
        $configs = [];
        if( empty($settings) ) {
            $this->ajaxFailed( 'fetch mail setting error'  );
        }
        foreach( $settings as $s ){
            $configs[$s['_key']] = $settingModel->formatValue( $s );
        }
        unset($settings);
        ini_set("magic_quotes_runtime", 0);
        require_once PRE_APP_PATH . '/vendor/phpmailer/phpmailer/PHPMailerAutoload.php';

        $data = [];
        try {
            $mail = new \PHPMailer(true);
            $mail->IsSMTP();
            $mail->CharSet = 'UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
            $mail->SMTPAuth = true; //开启认证
            $mail->Port = $configs['mail_port'];
            $mail->SMTPDebug = 2;
            $mail->Host = $configs['mail_host'];    //"smtp.exmail.qq.com";
            $mail->Username = $configs['mail_account'];     // "chaoduo.wei@ismond.com";
            $mail->Password = $configs['mail_password'];    // "Simarui123";
            $mail->Timeout = isset($configs['timeout']) ? $configs['timeout'] : 20;
            $mail->From = $configs['send_mailer'];
            $mail->FromName = $configs['send_mailer'];
            $mail->AddAddress($params['recipients']);
            $mail->Subject = $params['title'];
            $mail->Body = $params['content'];;
            $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示，可以省略
            $mail->WordWrap = 80; // 设置每行字符串的长度
            $mail->IsHTML($params['content_type']=='html');
            $ret = $mail->Send();
            if (!$ret) {
                $msg = 'Mailer Error: ' . $mail->ErrorInfo;
                $data['verbose'] = ob_get_contents();
                ob_clean();
                ob_end_clean();
                $this->ajaxFailed( $msg ,$data );
            }
        } catch (phpmailerException $e) {
            $msg = "邮件发送失败：" . $e->errorMessage();
            $data['verbose'] = ob_get_contents();
            ob_clean();
            ob_end_clean();
            $this->ajaxFailed( $msg ,$data );
        }  catch (\Exception $e) {
            $msg = "邮件发送失败：" . $e->errorMessage();
            $data['verbose'] = ob_get_contents();
            ob_clean();
            ob_end_clean();
            $this->ajaxFailed( $msg ,$data );
        }
        $data['verbose'] = ob_get_contents();
        ob_clean();
        ob_end_clean();
        $this->ajaxSuccess('send mail done.', $data );
    }

    public function email_queue(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'email';
        $data['left_nav_active'] = 'email_queue';
        $this->render('gitlab/admin/system_email_queue.php' ,$data );
    }

    /**
     * ajax请求队列列表
     */
    public function mail_queue_fetch()
    {
        $ret = [];
        $page = max( 1, (int) $_GET['page'] );
        if (empty($page)) {
            $page = 1;
        } else {
            $page = intval($page);
        }
        $conditions = [];
        if( isset($_GET['status']) && !empty( trimStr($_GET['status']) ) ){
            $conditions['status'] = trimStr($_GET['status']);
        }
        $logic = new MailQueueLogic( );
        $model  = MailQueueModel::getInstance();

        list( $ret['total'],$ret['pages'],$ret['current_page'],$ret['page_html'] ,$ret['page_size']) = $logic->getPageInfo( $conditions,$page  );
        $ret['queues'] = $logic->query( $conditions,  $page,  $model->primaryKey,'desc' );

        $this->ajaxSuccess('ok', $ret);

    }


    public function email_queue_error_clear(   ){

        $model  = MailQueueModel::getInstance();
        $conditions = [];
        $conditions['status'] = MailQueueModel::STATUS_ERROR;
        $ret =  $model->delete( $conditions );
        if( !$ret ){
            $this->ajaxFailed('server_error');
        }
        $this->ajaxSuccess('ok');
    }



    public function send_mail(){

        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'email';
        $data['left_nav_active'] = 'send_mail';
        $this->render('gitlab/admin/system_send_mail.php' ,$data );
    }

    public function send_mail_fetch(){

        $data = [];
        $model = new ProjectRoleModel();
        $roles =  $model->getAll();
        $data['roles'] = $roles;

        $model = new ProjectModel();
        $projects =  $model->getAll();
        $data['projects'] = $projects;

        $model = new GroupModel();
        $groups =  $model->getAll( false );
        $data['groups'] = $groups;

        $this->ajaxSuccess('ok',$data);

    }

    public function send_mail_post( $params=[] ){

        $error_msg = [];
        if( empty( $params ) ){
            $error_msg['tip'] = 'param_is_empty';
        }
        if( !isset( $params['send_to'] ) ){
            $error_msg['field']['send_to'] = 'value_is_empty';
        }
        if( !isset( $params['title'] ) ){
            $error_msg['field']['title'] = 'value_is_empty';
        }
        if( !isset( $params['content'] ) ){
            $error_msg['field']['content'] = 'value_is_empty';
        }
        if( !empty($error_msg) ){
            $this->ajaxFailed( $error_msg ,[],600);
        }

        $emails = [];
        $systemLogic = new SystemLogic();
        if( $params['send_to']=='project' ) {
            $emails = $systemLogic->getUserEmailByProjectRole( $params['to_project'] , $params['to_role'] );
        }

        if( $params['send_to']=='group' ) {
            $tmp = $systemLogic->getUserEmailByGroup( $params['to_group'] );
            $emails = $emails + $tmp;
            unset($tmp);
        }
        if( empty($emails) ){
            $this->ajaxFailed( 'user_no_found' );
        }
        list( $ret, $msg ) = $systemLogic->mail( $emails, $params['title'], $params['content'], $params['reply'], $params['content_type']  );
        unset( $params ,$systemLogic );
        if( $ret ){
            $this->ajaxSuccess('send_ok');
        }else{
            $this->ajaxFailed( $msg );
        }


    }


}