<?php
/**
+----------------------------------------------------------------------
| swoolefy framework bases on swoole extension development, we can use it easily!
+----------------------------------------------------------------------
| Licensed ( https://opensource.org/licenses/MIT )
+----------------------------------------------------------------------
| Author: bingcool <bingcoolhuang@gmail.com || 2437667702@qq.com>
+----------------------------------------------------------------------
*/

namespace Swoolefy\Core\Process;

use Swoole\Process;
use Swoole\Table;
use Swoolefy\Core\Table\TableManager;

class ProcessManager {

    use \Swoolefy\Core\SingletonTrait;
	
	private static $table_process = [
		// 进程内存表
		'table_process_map' => [
			// 内存表建立的行数,取决于建立的process进程数
			'size' => 256,
			// 字段
			'fields'=> [
				['pid','int', 10]
			]
		],
	];

	private static $processList = [];

    /**
     * __construct 
     */
	private function __construct() {
		TableManager::getInstance()->createTable(self::$table_process);
	}

	/**
	 * addProcess 添加一个进程
	 * @param string  $processName
	 * @param string  $processClass
	 * @param boolean $async
	 * @param array   $args
     * @throws
     * @return mixed
	 */
	public static function addProcess(string $processName, string $processClass, $async = true, array $args = [], $extend_data = null) {
		if(!TableManager::isExistTable('table_process_map')) {
			TableManager::getInstance()->createTable(self::$table_process);
		}

		$key = md5($processName);
        if(!isset(self::$processList[$key])){
            try{
                $process = new $processClass($processName, $async, $args, $extend_data);
                self::$processList[$key] = $process;
                return true;
            }catch (\Exception $e){
                throw new \Exception($e->getMessage(), 1);       
            }
        }else{
            throw new \Exception("you can not add the same process : $processName", 1);
        }
	}

	/**
	 * getProcessByName 通过名称获取一个进程
	 * @param  string $processName
	 * @return object
	 */
	public static function getProcessByName(string $processName) {
        $key = md5($processName);
        if(isset(self::$processList[$key])){
            return self::$processList[$key];
        }else{
            return null;
        }
    }

    /**
     * getProcessByPid 通过进程id获取进程
     * @param  int    $pid
     * @return object
     */
    public static function getProcessByPid(int $pid) {
        $table = TableManager::getTable('table_process_map');
        foreach ($table as $key => $item){
            if($item['pid'] == $pid){
                return self::$processList[$key];
            }
        }
        return null;
    }

    /**
     * setProcess 设置一个进程
     * @param string          $processName
     * @param AbstractProcess $process
     */
    public static function setProcess(string $processName, AbstractProcess $process) {
        $key = md5($processName);
        self::$processList[$key] = $process;
    }

    /**
     * reboot 重启某个进程
     * @param  string $processName
     * @return boolean
     */
    public static function reboot(string $processName) {
        $process = self::getProcessByName($processName);
        $kill_flag = $process->getSwoolefyProcessKillFlag();
        self::writeByProcessName($processName, $kill_flag);
    }

    /**
     * writeByProcessName 向某个进程写数据
     * @param  string $name
     * @param  string $data
     * @return boolean
     */
    public static function writeByProcessName(string $name,string $data) {
        $process = self::getProcessByName($name);
        if($process){
            return (bool)$process->getProcess()->write($data);
        }else{
            return false;
        }
    }

    /**
     * readByProcessName 读取某个进程数据
     * @param  string $name
     * @param  float  $timeOut
     * @return mixed
     */
    public static function readByProcessName(string $name, float $timeOut = 0.1) {
        $process = self::getProcessByName($name);
        if($process){
            $process = $process->getProcess();
            $read = array($process);
            $write = [];
            $error = [];
            $ret = swoole_select($read, $write, $error, $timeOut);
            if($ret){
                return $process->read(64 * 1024);
            }else{
                return null;
            }
        }else{
            return null;
        }
    }

}