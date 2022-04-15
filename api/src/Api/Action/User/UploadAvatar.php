<?php

declare(strict_types=1);

namespace App\Api\Action\User;

use App\Entity\User;
use App\Service\User\UploadAvatarService;
use Symfony\Component\HttpFoundation\Request;

class UploadAvatar{

    private uploadAvatarService $uploadAvatarService;

    public function __construct(uploadAvatarService $uploadAvatarService)
    {
        $this->uploadAvatarService = $uploadAvatarService;
        
    }

    public function __invoke(Request $request, User $user)
    {
        return $this->uploadAvatarService->uploadAvatar($request,$user);
    }
}