{extends file="layout.tpl"}

{block name="main"}
<div>
	<h1>Admin Uploader</h1>
	<form>
		<label for="lastname">last name: (OR eid:&lt;<em>eid</em>&gt;)</label>
		<input type="text" name="lastname" value="{$lastname}">
		<input type="submit" value="search">
	</form>
	<ul class="results">
		{foreach item=person from=$results}
		<li><a href="upload/{$person.eid}">{$person.name} : ({$person.unit}) | EID {$person.eid}</a></li>
		{/foreach}
	</ul>

	<h2>CVs uploaded by {$request->user->name}</h2>
	<ul>
		{foreach item=file from=$files}
		<li>{$file->orig_name} (faculty {$file->eid})</li>
		{/foreach}
	</ul>
</div>
{/block}
