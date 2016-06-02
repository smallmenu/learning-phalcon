<?php
/**
 * Member
 *
 * @author
 * @copyright
 */

namespace Demo\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Message;
use Phalcon\Security\Random;
use Phalcon\Mvc\Model\Validator\Email;
use Phalcon\Mvc\Model\Validator\Regex;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Member extends Model
{
    const VALID = 1;
    const UNVALID = 0;

    /**
     * onConstruct 每一个实例在创建的时候单独进行初始化
     */
    public function onConstruct()
    {
    }

    /**
     *
     * save()发生时事件调用顺序是
     * initialize,
     * onConstruct,
     * beforeValidation,
     * beforeValidationOnCreate,
     * afterValidationOnCreate,
     * afterValidation,
     * beforeSave,
     * beforeCreate/update,
     * afterCreate/update,
     * afterSave,
     */
    public function initialize()
    {
        // 定义行为，软删除
        $this->addBehavior(new SoftDelete(array('field' => 'status', 'value' => Member::UNVALID)));
        // 动态更新
        $this->useDynamicUpdate(true);
    }

    /**
     * 自定义非空返回信息，需要在这个阶段
     * Phalcon 默认会在beforeValidation() 后对字段 NOT NULL NO DEFAULT 自动进行校验
     * create/update 均验证
     *
     * @return bool
     */
    public function beforeValidation()
    {
        if (empty($this->username)) {
            $this->appendMessage(new Message('用户名不能为空', 'username', 'InvalidValue'));
            return false;
        }
        if (empty($this->email)) {
            $this->appendMessage(new Message('E-Mail不能为空', 'email', 'InvalidValue'));
            return false;
        }
        if (empty($this->password)) {
            $this->appendMessage(new Message('密码不能为空', 'password', 'InvalidValue'));
            return false;
        }
    }

    /**
     * Create 验证，目的是跳过NOT NULL NO DEFAULT
     *
     * @return bool
     */
    public function beforeValidationOnCreate()
    {
        // 不希望 Phalcon接下来自动进行 NOT NULL NO DEFAULT 校验
        $this->salt = true;
        $this->created = true;
        $this->updated = true;
    }

    /**
     * Update 验证，目的是跳过NOT NULL NO DEFAULT
     *
     * @return bool
     */
    public function beforeValidationOnUpdate()
    {

    }

    /**
     * 数据合法性验证
     *
     * @return bool
     */
    public function validation()
    {
        // 复杂用户名，中英文混合
        $this->validate(new Regex(
            array('field' => 'username', 'pattern' => '#^[A-Za-z0-9\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}]+$#u', 'message' => '用户名非法')
        ));

        // Email
        $this->validate(new Email(
            array('field' => 'email', 'message' => 'Email不合法')
        ));

        $this->validate(new Uniqueness(
            array("field" => "email", "message" => "Email已存在")
        ));

        return $this->validationHasFailed() != true;
    }

    /**
     * 与afterFetch配对，做一些转化处理
     *
     */
    public function beforeSave()
    {
    }

    /**
     * 插入前的自动处理
     *
     */
    public function beforeCreate()
    {
        // 字段未定义，并且是 NULL，在此处理
        $this->datetime = $this->updated = $this->created = date('Y-m-d H:i:s');

        $random = new Random();
        $this->salt = substr(uniqid($random->hex(6)), -6);
        $this->password = md5(md5($this->password) . $this->salt);
    }

    /**
     * 更新前的自动处理
     *
     */
    public function beforeUpdate()
    {
        $this->updated = date('Y-m-d H:i:s');
        $this->password = md5(md5($this->password) . $this->salt);
    }

    /**
     * 与beforeSave配对，做一些转化处理
     *
     */
    public function afterFetch()
    {
        $this->statusTxt = $this->status == Member::VALID ? '正常' : '禁用';
    }

}