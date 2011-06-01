{extends file="layout.tpl"}

{block name="content"}



<div class="controls">
	<a href="faculty/{$fac->eid}">return to {$fac->firstname} {$fac->lastname} faculty page</a>
</div>

<h2>{$fac->lastname}, {$fac->firstname} ({$fac->eid}) : CV Version</h2>
<div class="cv section">
	<a href="faculty/{$fac->eid}/file/{$file->id}">{$file->orig_name} (uploaded {$file->ago} by {$file->uploaded_by})</a> 
</div>

<div class="section versionform">
	<h4>version {$v->text_md5|truncate:6:''} (saved {$v->ago} by {$v->edited_by}){if $v->note} [{$v->note}]{/if}
		{if $request->user->is_admin}
		| <a href="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}/diff">diff</a> |
		<a href="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}/diff?context=1">diff2</a>
		{/if}
		{if $v->has_citations}|
		<a href="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}/lines">lines</a>
		{/if}
	</h4>
	<form method="post" action="faculty/{$fac->eid}/file/{$file->id}/versioner">
		<textarea name="text">{$v->text}</textarea>
		<p>
		<label>note:</label>
		<input type="text" name="note">
		</p>
		<input type="submit" value="save new version">
		<input type="submit" value="remove line breaks and save new version" name="remove">
		<!--
		<p class="select_submit">
		<input type="submit" value="run macro">
		<select name="macro">
			<option value="">select one:</option>
			<option value="">remove line breaks</option>
			<option value="">remove numbers at line start</option>
			<option value="">add author to each citation</option>
		</select>
		<p class="select_submit">
		<input type="submit" value="save as...">
		<select name="save_as">
			<option value="">select one:</option>
			<option value="">final citations per line version</option>
			<option value="">final creative works version</option>
			<option value="">...</option>
		</select>
		-->
		</p>
	</form>
	{if $v->linebreaks_removed}
	{if !$v->has_citations}

	<div class="controls">
		<form method="post" action="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}/lines">
			<input type="submit" value="convert to lines">
		</form>
	</div>
	{/if}
	{/if}
	<div class="clear"></div>
</div>

{if $request->user->eid == $v->edited_by}
<div class="controls">
	<form method="delete" action="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}">
		<input type="submit" value="delete this version">
	</form>
</div>
{/if}

<div class="clear"></div>

{/block}
