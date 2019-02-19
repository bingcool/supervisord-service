<?php
/**
 * Created by PhpStorm.
 * User: bingcool
 * Date: 2019/2/17
 * Time: 12:06
 */
namespace App\Controller;

use Swoolefy\Core\Controller\BController;
use Swoolefy\Core\Swfy;

class UploadController extends BController {

    private $ret_code_miss_supervisord = 20001; // 没配置supervisord
    private $ret_code_miss_path = 20002; // 没配置supervisord['path']

    /**
     * uploadFile
     * @return mixed
     */
    public function uploadFile() {
        // 获取post文件内容
        $streamData = $this->getRawContent();
        $filename = $this->getQueryParams('filename');
        $ext = $this->getQueryParams('ext');
        $program = $this->getQueryParams('program');

        if(!isset($program)) {
            $program = "Test1";
        }
        $path = $this->getQueryParams('path');

        if(!isset($path)) {
            $app_conf = Swfy::getAppConf();
            if(!isset($app_conf['supervisord'])) {
                $data = [
                    'ret' => $this->ret_code_miss_supervisord,
                    'msg' => "你需要在Config/config.php在配置supervisord",
                    'data' => ''
                ];
                $this->returnJson($data);
                return;
            }else {
                $supervisord_ini_path = $app_conf['supervisord']['path'];
                if(!empty($supervisord_ini_path)) {
                    $path = '/'.trim($supervisord_ini_path,'/').'/';
                }else {
                    $data = [
                        'ret' => $this->ret_code_miss_path,
                        'msg' => "你需要在Config/config.php在配置supervisord['path']",
                        'data' => ''
                    ];
                    $this->returnJson($data);
                    return;
                }

                $username = $app_conf['supervisord']['username'];
                $password = $app_conf['supervisord']['password'];
            }
        }

        $file_path = $path.$filename.'.'.$ext;
        if(!empty($streamData)) {
            file_put_contents($file_path, $streamData);
        }
        chmod($file_path, 0755);

        $shell_reread = "supervisorctl -u {$username} -p {$password} reread {$program}";

        $shell_update = "supervisorctl -u {$username} -p {$password} update {$program}";

        $this->execSupervisorCtl($shell_reread);
        $result = $this->execSupervisorCtl($shell_update);

        if($result['code'] == 0) {
            $data = [
                'ret' => 0,
                'msg' => $result['output'],
                'data' => $result['signal']
            ];

            if(false !== stripos($result['output'], "ERROR: no such group") || false !== stripos($result['output'], "no such group")) {
                $data = [
                    'ret' => 1,
                    'msg' => "program:{$program} 不存在,请查看配置文件{$filename}的[program]",
                    'data' => $result['signal']
                ];
            }
            
        }else {
            // 用户名或密码认证失败
            if($result['code'] == 2) {
                $data = [
                    'ret' => $result['code'],
                    'msg' => '用户名或密码认证失败',
                    'data' => $result['signal']
                ];
            }else {
                $data = [
                    'ret' => $result['code'],
                    'msg' => $result['output'],
                    'data' => $result['signal']
                ];
            }
        }
        $this->returnJson($data);
    }

    /**
     * @param string|null $shell
     * @return bool
     */
    public function execSupervisorCtl(string $shell = null) {
        return \co::exec($shell);
    }



}