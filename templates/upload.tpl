{extends file="layout.tpl"}

{block name="main"}
<div>
	<div class="controls">
	<a href="faculty/{$eid}">go to {$name}'s dashboard</a> |
	<a href="upload/search">return to search</a> 
	</div>
	{if $msg}
	<h3 class="msg">{$msg}</h3>
	{else}
	<h1>Admin Uploader</h1>
	<form action="upload/file" method="post" enctype="multipart/form-data">
		<!--
		<label for="title">title</label>
		<p>
		<input type="text" name="title"/>
		</p>
		-->
		<p>
		<label for="uploaded_file">attach a file (MS Word or PDF format)</label>
		<input type="file" name="uploaded_file" size="50"/>
		</p>
		<p>
		<input type="text" readonly value="{$eid}" name="cv_eid">
		</p>
		<p>
		<input type="submit" value="upload {$name}'s CV"/>
		</p>
	</form>

	<h1>OR</h1>
	<h4>paste URL</h4>
	<form action="upload/{$eid}/ingester" method="post">
		<p>
		<label for="url">url</label>
		<input type="text" name="url" size="50"/>
		<input type="submit" value="get HTML"/>
		</p>
	</form>

	<h3>{$name}'s ({$eid}) Files:</h3>
	<ul>
		{foreach item=file from=$files}
		<li>{$file->name}</li>
		{/foreach}
	</ul>


	{/if}

</div>
{/block}
