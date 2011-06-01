{extends file="layout.tpl"}

{block name="content"}



<div class="controls" id="facinfo">
<a href="faculty/{$fac->eid}">{$fac->firstname} {$fac->lastname} faculty page</a> |
<a href="http://dev.laits.utexas.edu/publications/uploader/admin/uploader/{$fac->eid}">upload a file for {$fac->eid}</a> |
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
	</li>
	{/foreach}
</ul>
</div>


{/block}
