{extends file="layout.tpl"}

{block name="content"}
<div>
	<h1>Administration</h1>
	<ul class="operations">
		<li><a href="user/settings">my user settings</a></li>
		{if $request->user->is_admin}
		<li><a href="directory">add a user</a></li>
		<li><a href="admin/users">list users</a></li>
		<li><a href="admin/reports">reports</a></li>
		{/if}
	</ul>
</div>
{/block}
