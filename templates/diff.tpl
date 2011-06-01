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
	<h4>version {$v->text_md5|truncate:6:''} (saved {$v->ago} by {$v->edited_by}){if $v->note} [{$v->note}]{/if} |
	<a href="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}">edit</a>
</h4>
</div>

<div class="section diff">
	<pre>
	{$diff|default:'no difference'}
</pre>
</div>

<div class="clear"></div>

{/block}
