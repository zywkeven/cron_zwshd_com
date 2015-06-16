<?php
/**
 * 抽象组件角色
 * @author keven.zhong
 * @Version 1.0 At 2015-02-03
*/
interface Component_Component {

    /**
     * 返回自己的实例
     */
    public function getComposite();

    /**
     * 操作方法
    */
    public function operation($param = null);
}