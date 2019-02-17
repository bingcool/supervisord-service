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

namespace Swoolefy\Core\Task;

use Swoolefy\Core\BService;

class TaskService extends BService {
    /**
     * $task_id 任务的ID
     * @var null
     */
    public $task_id;

    /**
     * $from_worker_id 记录当前任务from的woker投递
     * @var null
     */
    public $from_worker_id;

    /**
     * setTaskId
     * @param int $task_id
     */
    public function setTaskId(int $task_id) {
        $this->task_id = $task_id;
    }

    /**
     * setFromWorkerId
     * @return int
     */
    public function setFromWorkerId(int $from_worker_id) {
        $this->from_worker_id = $from_worker_id;
    }

    /**
     * getTaskId
     * @return int
     */
    public function getTaskId() {
        return $this->task_id;
    }

    /**
     * getFromWorkerId
     * @return int
     */
    public function getFromWorkerId() {
        return $this->from_worker_id;
    }
}