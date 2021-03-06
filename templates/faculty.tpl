{extends file="layout.tpl"}

{block name="content"}



<div class="controls" id="facinfo">
<a href="faculty/{$fac->eid}">{$fac->firstname} {$fac->lastname} faculty page</a> |
<a href="upload/{$fac->eid}">upload a file for {$fac->eid}</a> |
{if $on_watchlist}
<a href="user/{$request->user->eid}/watchlist/{$fac->eid}" class="delete">remove from watchlist</a> 
{else}
<a href="user/{$request->user->eid}/watchlist/{$fac->eid}" class="put">add to watchlist</a> 
{/if}
</div>

<h2>{$fac->lastname}, {$fac->firstname} ({$fac->eid})</h2>
<div class="cvs section">
<h3>uploaded CVs</h3>
<ul>
	{foreach item=file from=$files}
	<li>
	<a href="faculty/{$fac->eid}/file/{$file->id}">{$file->orig_name} (uploaded {$file->ago} by {$file->uploaded_by})</a> |
	{$file->status|default:'no status'} |
	{$file->versions|@count} version(s) |
	<a href="file/{$fac->eid}/{$file->name}/download">download</a>
	{if $file->is_preferred}
	| <span class="flag">preferred</span>
	{/if}
	</li>
	{/foreach}
</ul>


<h2>report a problem</h2>

<form method="post" action="faculty/{$fac->eid}/problem">
<p>
<label>Problem Brief Description<span class="current">[{$fac->problem_note}]</span></label>
<input type="text" name="problem_note" value="{$fac->problem_note}">
<br>
<select name="problem_code">
<option>problem codes:</option>
<option>NO_CV</option>
<option>READY</option>
<option>NOT_READY</option>
</select>
</p>
<input type="submit" value="flag problem">
</form>

{if $pref_versions|@count}
<h2>versions marked preferred (<a href="faculty/{$fac->eid}/preflines">view all</a>)</h2>


<ul>
	{foreach item=v from=$pref_versions}
	<li>
	<a href="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}">{$v->text_md5|truncate:6:''} (saved {$v->ago} by {$v->edited_by})</a> 
	{if $v->has_citations} |
	<a href="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}/lines" class="linesLink">lines</a>
	{/if}
	{if $v->is_from_pref_cv} 
	| <span class="flag">from preferred cv</span>
	{/if}
	</li>
	{/foreach}
</ul>
{if $pref_versions|@count > 1}
<form action="faculty/{$fac->eid}/dedup" method="post">
	<input type="submit" value="run deduplication routine">
	<a href="faculty/{$fac->eid}/poss_dups">View Possible Duplicates</a> (count: {$fac->dup_count})
</form>
{/if}
	{/if}


</div>
{/block}
