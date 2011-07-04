{extends file="layout.tpl"}

{block name="content"}



<div class="controls">
<a href="faculty/{$fac->eid}">return to {$fac->firstname} {$fac->lastname} faculty page</a>
</div>

<h2>{$fac->lastname}, {$fac->firstname} ({$fac->eid}) : Uploaded CV</h2>
<div id="cv" class="cv section">
	{$file->orig_name} (uploaded {$file->ago} by {$file->uploaded_by}) | 
	{$file->status|default:'no status'} |
	{$file->versions|@count} version(s) |
	<a href="file/{$fac->eid}/{$file->name}/download">download</a> |
	<a href="#" class="toggle" id="toggleCVMeta">edit metadata</a>

<div class="cvmeta" id="targetCVMeta">
<form method="post" action="faculty/{$fac->eid}/file/{$file->id}/metadata">
<label>Status <span class="current">[{$file->status}]</span></label>
<select name="status">
<option value="">none</option>
<option {if $file->status == 'complete'}selected{/if} value="complete">complete</option>
<option {if $file->status == 'incomplete'}selected{/if} value="incomplete">incomplete</option>
</select>
<label>Date on CV <span class="current">[{$file->date_on_cv}]</span></label>
<input type="text" name="date_on_cv" value="{$file->date_on_cv}">
<label>Note <span class="current">[{$file->note}]</span></label>
<input type="text" name="note" value="{$file->note}">
<p>
<input type="submit" value="update info">
</p>
</form>

<div class="controls">
<a href="http://dev.laits.utexas.edu/publications/workflow/faculty/{$fac->eid}/file/{$file->id}">view on dev</a> |
<a href="http://www.laits.utexas.edu/publications/workflow/faculty/{$fac->eid}/file/{$file->id}">view on www</a> 
	<form method="get" action="faculty/{$fac->eid}/file/{$file->id}">
<input type="hidden" name="reconvert" value="1">
		<input type="submit" value="reconvert file">
	</form>

{if $request->user->eid == $file->uploaded_by}
{if 0 == $file->versions|@count}
	<form method="delete" action="faculty/{$fac->eid}/file/{$file->id}">
		<input type="submit" value="delete this file">
	</form>
{/if}
{/if}
</div>

<h2>preferred CV</h2>
{if $file->is_preferred}
[This is the preferred CV for {$fac->eid}]
{else}
<form method="post" action="faculty/{$fac->eid}/file/{$file->id}/preferred">
<p>
<input type="submit" value="flag this as the preferred CV for {$fac->eid}">
</p>
</form>
{/if}

<h2>report a problem</h2>

<form method="post" action="faculty/{$fac->eid}/file/{$file->id}/problem">
<p>
<label>Problem Brief Description<span class="current">[{$file->problem_note}]</span></label>
<input type="text" name="problem_note" value="{$file->problem_note}">
<br>
<select name="problem_code">
<option>problem codes:</option>
<option>EDIT_PAIN</option>
<option>EXT_DB</option>
<option>GARBLED_TEXT</option>
<option>MISASSIGNED</option>
<option>MISC</option>
<option>NO_TEXT</option>
<option>OCR_FAILS</option>
<option>OCR_PAIN</option>
<option>OCR</option>
<option>URL_UPLOAD</option>
</select>
</p>
<input type="submit" value="flag problem">
</form>

<div class="clear"></div>
</div>

</div>

<div class="versions section">
<h3>versions <span class="hinfo">(most recent first)</span></h3>
<ul>
	{foreach item=v from=$file->versions}
	<li>
	<a href="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}">{$v->text_md5|truncate:6:''} (saved {$v->ago} by {$v->edited_by})</a> 
	{if $v->note} [{$v->note}] {/if}
		{if $request->user->is_admin} |
	<a href="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}/diff">diff</a> |
	<a href="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}/diff?context=1">diff2</a>
{/if}
	{if $v->has_citations} |
	<a href="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}/lines" class="linesLink">lines</a>
{/if}
	{if $v->is_preferred} | <span class="flag">preferred</span>{/if}
	</li>
	{/foreach}
</ul>

<form method="post" action="faculty/{$fac->eid}/file/{$file->id}/versioner">
<input type="submit" value="create new unedited version">
</form>


</div>

<div id="raw" class="filetext section">
<a href="#" class="toggle" id="toggleRaw">show/hide text</a> ({$file->rawtext_size} bytes)
<pre class="hide" id="targetRaw">
{$file->rawtext}
</pre>
</div>


{/block}
