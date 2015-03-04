<?php
/**
 * 核心异常类 请求权限验证异常
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2014-03-04
 */
namespace Core\Exception;

class RequestPermissionException extends \Core\Exception {

    protected $code    = 914;

    protected $message = "请求权限验证异常";

}