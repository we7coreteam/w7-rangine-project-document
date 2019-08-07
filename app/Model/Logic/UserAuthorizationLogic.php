<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\App\Model\Logic;

use W7\App\Event\ChangeAuthEvent;
use W7\App\Model\Entity\PermissionDocument;
use W7\App\Model\Entity\User;

class UserAuthorizationLogic extends BaseLogic
{
	public function inviteUser($user_id, $document_id)
	{
		$exist = PermissionDocument::where('user_id', $user_id)->where('document_id', $document_id)->first();
		if ($exist) {
			throw new \Exception('该用户已经拥有操作该文档的权限!');
		}
		$result = PermissionDocument::create(['user_id' => $user_id,'document_id' => $document_id]);
		ChangeAuthEvent::instance()->attach('user_id', $user_id)->attach('document_id', $document_id)->dispatch();
		return $result;
	}

	public function leaveDocument($user_id, $document_id)
	{
		PermissionDocument::where('user_id', $user_id)->where('document_id', $document_id)->delete();
		ChangeAuthEvent::instance()->attach('user_id', $user_id)->attach('document_id', $document_id)->dispatch();
		return true;
	}

	public function getUserAuthorizations($user_id)
	{
		$cacheAuth = cache()->get('auth_'.$user_id);
		if ($cacheAuth) {
			return $cacheAuth;
		}
		$user = User::find($user_id);
		if ($user) {
			if ($user->has_privilege) {
				cache()->set('auth_'.$user_id, APP_AUTH_ALL, 24*3600);
				return APP_AUTH_ALL;
			}
		} else {
			return [];
		}
		$auth['document'] = PermissionDocument::where('user_id', $user_id)->pluck('document_id')->toArray();
		cache()->set('auth_'.$user_id, $auth, 24*3600);
		return $auth;
	}

	public function getDocumentUsers($document_id)
	{
		$cacheDocumentUsers = cache()->get('document_users_'.$document_id);
		if ($cacheDocumentUsers) {
			return $cacheDocumentUsers;
		}
		$documentUsers = PermissionDocument::where('document_id', $document_id)->pluck('user_id')->toArray();
		cache()->set('document_users_'.$document_id, $documentUsers, 24*3600);
		return $documentUsers;
	}
}
