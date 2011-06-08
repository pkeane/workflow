{extends file="layout.tpl"}

{block name="content"}
<h1>Tasks</h1>

<ul>
	<li>
	<form action="admin/file_privs" method="post">
		<input type="submit" value="fix file privileges">
	</form>
	</li>
</ul>
{/block}
