<?php
/**
 * 树枝组件角色
 * @author keven.zhong
 * @Version 1.0 At 2015-02-03
 */
class Component_Composite implements Component_Component{

    private $_composites;
    
    private $_countHandle = 1;
    
    private $_handle = Array();
    
    private $_lastHandle = null;    

    public function __construct() {
        $this->_composites = array();
    }

    public function getComposite() {
        return $this;
    }

    /**
     * 示例方法，调用各个子对象的operation方法
     */
    public function operation($param = null) {
        foreach ($this->_composites as $composite) {
            $composite->setComposite($this);
            $composite->operation($param);
        }
    }

    /**
     * 聚集管理方法 添加一个子对象
     * @param Component_Component $component  子对象
     */
    public function add(Component_Component $component) {
        $this->_composites[] = $component;
    }

    /**
     * 聚集管理方法 删除一个子对象
     * @param Component_Component $component  子对象
     * @return boolean  删除是否成功
     */
    public function remove(Component_Component $component) {
        foreach ($this->_composites as $key => $row) {
            if ($component == $row) {
                unset($this->_composites[$key]);
                return true;
            }
        }
        return false;
    }

    /**
     * 聚集管理方法 返回所有的子对象
     * @return Ambigous <multitype:, Component_Component>
     */
    public function getChild() {
        return $this->_composites;
    }
    
}