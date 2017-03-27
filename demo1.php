<?php
/**
 * 单次调用:有一个很大的工作需要分片处理 
 */
require_once 'MultiProcess.class.php';

$mp = new MultiProcess(3, 'myProcessName'); // 4代表子进程数, 'myProcessName'是进程的名字
$mp->master(function ($mp) {
    $mp->submit([0, 1000]);
    $mp->submit([1000, 2000]);
    $mp->submit([2000, 3000]);
    $mp->submit([3000, 4000]);
    $mp->submit([4000, 5000]);

    $mp->wait();    // 等待所有任务执行完毕, 可以带一个timeout参数代表超时时间毫秒数, 超过后将强行终止还没完成的任务并返回
})->slave(function ($params, $mp) {
    list ($from, $to) = $params;
    file_read_by_line($from, $to, 'demo.txt');
});
