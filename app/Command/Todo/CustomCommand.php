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

namespace W7\App\Command\Todo;

use W7\App\Model\Entity\Article\Article;
use W7\App\Model\Entity\Message\Message;
use W7\App\Model\Entity\Message\Text;
use W7\Console\Command\CommandAbstract;
use W7\App\Model\Entity\User;
use Illuminate\Support\Str;

class CustomCommand extends CommandAbstract
{
	protected $description = '上线需要执行的脚本';

	protected function configure()
	{
		$this->setName('todo:custom');
	}

	protected function handle($options)
	{
		go(function () {
			$this->handleRepeatUsername();
			$this->handleMessageTime();
			$this->handleMessageContent();
			$this->output->success('success');
		});
	}

	// 修改重复用户名
	public function handleRepeatUsername()
	{
		$users = User::whereIn('username', function ($query) {
			$query->select('username')->from('user')->groupBy('username')->havingRaw('COUNT(username) > 1');
		})->get();
		$users->map(function ($item) {
			$item->username = $item->username . Str::random(6);
			$item->save();
		});
		return $users;
	}

	// 修改消息通知时间数据
	public function handleMessageTime()
	{
		$messages = Message::where([
			['target_type', '=', 'remind_article'],
			['created_at', '=', 2021],
			['updated_at', '=', 2021]
		])->get();
		$messages->map(function ($itme) {
			$time = 1621590891;
			$article = Article::where('id', $itme->target_id)->first();
			if ($article) {
				$time = $article->updated_at;
			}
			$itme->created_at = $itme->updated_at = $itme->text->created_at = $itme->text->updated_at = $time;
			$itme->save();
			$itme->text->save();
		});
		return $messages;
	}

	// 消息通知内容添加识别标签
	public function handleMessageContent()
	{
		$texts = Text::get();
		$texts->map(function ($item) {
			$content = $item->content;
			$start = mb_strpos($content, '《');
			$end = mb_strripos($content, '》') + 1;
			$item->content = mb_substr($content, 0, $start) . "<span class='article_title'>" . mb_substr($content, $start, $end - $start) . '</span>' . mb_substr($content, $end);
			$item->save();
		});
		return $texts;
	}
}
