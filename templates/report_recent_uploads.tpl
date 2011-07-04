{extends file="layout.tpl"}

{block name="content"}
<h1>Reports</h1>


<h2>Recent Uploads</h2>




<ul>
	{foreach item=file from=$files}
	<li>
	<a href="faculty/{$file->eid}/file/{$file->id}">{$file->orig_name} (uploaded {$file->ago} by {$file->uploaded_by})</a> |
	{$file->status|default:'no status'} 
	{if $file->is_preferred}
	| <span class="flag">preferred</span>
	{/if}
	</li>
	{/foreach}
</ul>

{/block}
