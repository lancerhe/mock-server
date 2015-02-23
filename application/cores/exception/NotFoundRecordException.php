<?php
/**
 * 核心异常类 数据资源不存在
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-23
 */
namespace Core\Exception;

class NotFoundRecordException extends \Core\Exception {

    protected $code    = 930;

    protected $message = "数据资源未找到";

}
