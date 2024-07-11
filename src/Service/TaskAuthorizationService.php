<?php

// src/Service/TaskAuthorizationService.php

namespace App\Service;

use App\Entity\Task;
use Symfony\Component\Security\Core\Security;

class TaskAuthorizationService
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function canDelete(Task $task): bool
    {
        $role_is_admin = in_array('ROLE_ADMIN', $this->security->getUser()->getRoles());
        $username = $task->getUser()->getUsername();

        if ('anonyme' == $username && !$role_is_admin) {
            return false;
        } else {
            if ('anonyme' != $username && $task->getUser()->getId() !== $this->security->getUser()->getId()) {
                return false;
            }
        }

        return true;
    }
}
