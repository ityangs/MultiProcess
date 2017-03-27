# MultiProcess PHP多进程控制类库
Simple php multi-process control library  精简的PHP多进程控制类库。多进程类库封装了主进程和子进程之间的通信，以及日志打印，还有错误处理等基础功能，方便地利用系统的多个cpu来完成一些异步任务

## 使用步骤

### 安装

引入多进程类库
```php

require_once 'MultiProcess.class.php';

```

### 使用案例


#### 单次调用:有一个很大的工作需要分片处理 

```php
$mp = new MultiProcess(3, 'myProcessName'); // 3代表子进程数, 'myProcessName'是进程的名字
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
```





#### 循环调用的时候:从任务队列获取任务常驻内存

```php
$mp = new MultiProcess(3, 'myProcessName'); // 代表子进程数, 'myProcessName'是进程的名字

$url = "http://www.baidu.com/";
$mp->master(function ($mp)
{
    $data = "abc";
    $url = "http://www.baidu.com/";
    // 主进程的方法请包裹在master里
    while ($mp->loop(1000)) { // 100为等待的毫秒数
        $mp->submit($url, function ($data)
        { // 使用submit方法将其提交到一个空闲的进程，如果没有空闲的，系统会自动等待
            echo $data;
        });
    }
})
    ->slave(function ($url, $mp)
{
    $mp->log('fetch %s', $url); // 使用内置的log方法，子进程的log也会被打印到主进程里
    return http_request($url); // 直接返回数据，主进程将在回调中收到
});
```



注意：
>可用PHP命令行测试该案例

