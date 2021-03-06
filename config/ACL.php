<?php

/**
 * Class ACL
 * @desc Do not touch except you know what you are doing
 */
class ACL extends \Zend\Permissions\Acl\Acl
{
    const ACL_DEFAULT_ROLE_ADMIN = 'admin';
    const ACL_DEFAULT_ROLE_MEMBER = 'member';
    const ACL_DEFAULT_ROLE_GUEST = 'guest';
    const ACL_UNDELETABLE_ROLES = [ACL::ACL_DEFAULT_ROLE_ADMIN, ACL::ACL_DEFAULT_ROLE_GUEST, ACL::ACL_DEFAULT_ROLE_MEMBER];
    const ACL_WILDCARD = '*';

    const ACL_DEFAULT_RESOURCES = [
        ACL::ACL_WILDCARD,
        '/401',
        '/403',
        '/404',
        '/500',
        '/',
        '/login',
        '/logout',

        '/profile',
        '/profile/credentials',

        '/instance',
        '/instance/edit',

        '/logs[/{sid}]',

        '/servers',
        '/servers/create',
        '/servers/{sid}',
        '/servers/select/{sid}',
        '/servers/deselect',
        '/servers/delete/{sid}',
        '/servers/start/{sid}',
        '/servers/stop/{sid}',
        '/servers/send/{sid}',
        '/servers/edit/{sid}',

        '/snapshots/{sid}',
        '/snapshots/create/{sid}',
        '/snapshots/deploy/{sid}/{name}',
        '/snapshots/delete/{sid}/{name}',

        '/tokens/{sid}',
        '/tokens/add/{sid}',
        '/tokens/delete/{sid}/{token}',

        '/online/{sid}',
        '/online/{sid}/{clid}',
        '/online/poke/{sid}/{clid}',
        '/online/kick/{sid}/{clid}',
        '/online/ban/{sid}/{clid}',
        '/online/send/{sid}/{clid}',
        '/online/move/{sid}/{clid}',

        '/clients/{sid}',
        '/clients/{sid}/{cldbid}',
        '/clients/delete/{sid}/{cldbid}',
        '/clients/ban/{sid}/{cldbid}',
        '/clients/send/{sid}/{cldbid}',

        '/channels/{sid}',
        '/channels/create/{sid}',
        '/channels/{sid}/{cid}',
        '/channels/edit/{sid}/{cid}',
        '/channels/delete/{sid}/{cid}',
        '/channels/send/{sid}/{cid}',
        '/channels/files/delete/{sid}/{cid}',

        '/groups/{sid}',

        '/servergroups/{sid}/{sgid}',
        '/servergroups/create/{sid}',
        '/servergroups/delete/{sid}/{sgid}',
        '/servergroups/rename/{sid}/{sgid}',
        '/servergroups/remove/{sid}/{sgid}/{cldbid}',
        '/servergroups/add/{sid}/{sgid}',

        '/channelgroups/{sid}/{cgid}',
        '/channelgroups/create/{sid}',
        '/channelgroups/delete/{sid}/{cgid}',
        '/channelgroups/rename/{sid}/{cgid}',

        '/bans/{sid}',
        '/bans/delete/{sid}/{banId}',

        '/complains/{sid}',
        '/complains/delete/{sid}/{tcldbid}',

        '/passwords/{sid}',
        '/passwords/add/{sid}',
        '/passwords/delete/{sid}',
    ];

    const ACL_DEFAULT_ALLOWS = [
        ACL::ACL_DEFAULT_ROLE_ADMIN => [ACL::ACL_WILDCARD],
        ACL::ACL_DEFAULT_ROLE_MEMBER => [
            '/logout',
        ],
        ACL::ACL_DEFAULT_ROLE_GUEST  => [
            '/login',
            '/',
            '/401',
            '/403',
            '/404',
            '/500',
        ],
    ];

    const ACL_DEFAULT_DENIES = [
        ACL::ACL_DEFAULT_ROLE_ADMIN => ['/login'],
        ACL::ACL_DEFAULT_ROLE_MEMBER    => ['/login'],
    ];

    public function __construct()
    {
        $res = self::ACL_DEFAULT_RESOURCES;
        $allows = self::ACL_DEFAULT_ALLOWS;
        $denies = self::ACL_DEFAULT_DENIES;

        // roles
        $this->addRole(self::ACL_DEFAULT_ROLE_GUEST);
        $this->addRole(self::ACL_DEFAULT_ROLE_MEMBER, self::ACL_DEFAULT_ROLE_GUEST);
        $this->addRole(self::ACL_DEFAULT_ROLE_ADMIN);

        // resource
        foreach ($res as $resource) {
            $this->addResource($resource);
        }

        // allows
        foreach ($allows as $role => $paths) {

            foreach ($paths as $path) {

                if (empty($path) || $path === '' || $path === ACL::ACL_WILDCARD) {
                    $this->allow($role);
                } else {
                    $this->allow($role, $path);
                }
            }
        }

        // denies
        foreach ($denies as $role => $paths) {

            foreach ($paths as $path) {

                if (empty($path) || $path === '') {
                    $this->deny($role);
                } else {
                    $this->deny($role, $path);
                }
            }
        }
    }

    /**
     * Get all children for a role
     *
     * @param $role
     * @return array
     */
    public function getAllChildrenForRole($role)
    {
        $children = array();
        foreach ($this->getRoles() as $inherit) {
            if($this->inheritsRole($role, $inherit)) {
                $children[] = $inherit;
            }
        }
        return $children;
    }

    /**
     * Can $currentRole access resources for $targetRole
     *
     * @param $currentRole
     * @param $targetRole
     * @return bool
     */
    public function isPermitted($currentRole, $targetRole)
    {
        $children = $this->getAllChildrenForRole($targetRole);

        if ($targetRole == $currentRole || !in_array($currentRole, $children)) {
            return true;
        } else {
            return false;
        }
    }
}