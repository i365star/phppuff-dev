<?php

/**
 * Created by PhpStorm.
 * User: lijunpeng
 * Date: 2016/12/15
 * Time: 11:04
 */
namespace phppuff\process\worker;

use phppuff\process\Runnable;

/**
 * 任务的载体，进程与任务建立的关系，在进程中执行，执行的是任务
 * Interface IWorker
 * @package phppuff\process\worker
 */
interface IWorker extends Runnable {

}