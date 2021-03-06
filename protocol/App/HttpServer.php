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

namespace protocol\App;

use Swoolefy\Core\Swfy;

class HttpServer extends \Swoolefy\Http\HttpAppServer {

	/**
	 * __construct 初始化
	 * @param array $config
	 */
	public function __construct(array $config=[]) {
		parent::__construct($config);
	}

	/**
	 * onWorkerStart 
	 * @param   object $server    
	 * @param   int    $worker_id 
	 * @return  void
	 */
	public function onWorkerStart($server, $worker_id) {}

	/**
	 * onPipeMessage 
	 * @param    object  $server
	 * @param    int     $src_worker_id
	 * @param    mixed   $message
	 * @return   void
	 */
	public function onPipeMessage($server, $from_worker_id, $message) {}

}	