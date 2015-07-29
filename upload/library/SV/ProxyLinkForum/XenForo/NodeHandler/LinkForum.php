<?php
class SV_ProxyLinkForum_XenForo_NodeHandler_LinkForum extends XFCP_SV_ProxyLinkForum_XenForo_NodeHandler_LinkForum
{
    public function renderNodeForTree(XenForo_View $view, array $node, array $permissions,
        array $renderedChildren, $level
    )
    {
        if (empty($node['sv_is_proxy']))
        {
            return parent::renderNodeForTree($view, $node, $permissions, $renderedChildren, $level);
        }

        $templateLevel = ($level <= 2 ? $level : 'n');

        return $view->createTemplateObject('node_forum_level_' . $templateLevel, array(
            'level' => $level,
            'forum' => $node,
            'renderedChildren' => $renderedChildren
        ));
    }
    public function getPushableDataForNode(array $node, array $childPushable, array $permissions)
    {
        if (empty($node['sv_is_proxy']))
        {
            return parent::getPushableDataForNode($node, $childPushable, $permissions);
        }
        if (!XenForo_Permission::hasContentPermission($permissions, 'viewOthers'))
        {
// todo
            //return $this->_compileForumLikePushableData(array('privateInfo' => true), $childPushable);
        }

        return $this->_getForumLikePushableData($node, $childPushable);
    }

    public function getExtraDataForNodes(array $nodeIds)
    {
        $userId = XenForo_Visitor::getUserId();
        $permissionCombinationId = XenForo_Visitor::getPermissionCombinationId();
        $forumFetchOptions = array('readUserId' => $userId, 'permissionCombinationId' => $permissionCombinationId);

        return $this->_getForumModel()->getExtraForumDataForLinkNodes($nodeIds, $forumFetchOptions);
    }

    public function prepareNode(array $node)
    {
        if (empty($node['sv_is_proxy']))
        {
            return parent::prepareNode($node);
        }
        return $this->_getForumModel()->prepareForum($node);
    }


    protected $_forumModel = null;
    protected function _getForumModel()
    {
        if ($this->_forumModel === null)
        {
            $this->_forumModel = XenForo_Model::create('XenForo_Model_Forum');
        }

        return $this->_forumModel;
    }
}
