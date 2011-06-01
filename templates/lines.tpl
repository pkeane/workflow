{extends file="layout.tpl"}

{block name="content"}



<div class="controls">
	<a href="faculty/{$fac->eid}">return to {$fac->firstname} {$fac->lastname} faculty page</a>
</div>

<h2>{$fac->lastname}, {$fac->firstname} ({$fac->eid}) : CV Version</h2>
<div class="cv section">
	<a href="faculty/{$fac->eid}/file/{$file->id}">{$file->orig_name} (uploaded {$file->ago} by {$file->uploaded_by})</a> 
</div>

<div class="section lines" id="lines">
	<ul>
		{foreach item=line from=$lines}
		<li id="line{$line->id}" {if $line->is_creative}class="creative"{/if} {if $line->is_section}class="section"{/if}>
		<div class="operators">
			<a href="line/{$line->id}.json" class="toggle" id="toggleForm{$line->id}">[view/edit]</a> 
			{if $line->is_creative}
			<a href="line/{$line->id}/is_creative/0" class="put">[creative-]</a> 
			{else}
			<a href="line/{$line->id}/is_creative/1" class="put">[creative+]</a> 
			{/if}
			<p>
			{if $line->is_hidden}
			<a href="line/{$line->id}/is_hidden/0" class="put">[unhide]</a> 
			{else}
			<a href="line/{$line->id}/is_hidden/1" class="put">[hide]</a> 
			{/if}
			{if $line->is_peer}
			<a href="line/{$line->id}/is_peer/0" class="put">[peer-]</a> 
			{else}
			<a href="line/{$line->id}/is_peer/1" class="put">[peer+]</a> 
			{/if}
			{if $line->is_section}
			<a href="line/{$line->id}/is_section/0" class="put">[section-]</a> 
			{else}
			<a href="line/{$line->id}/is_section/1" class="put">[section+]</a> 
			{/if}
			</p>
		</div>
		{if $line->revised_text}
		<div class="line">{$line->revised_text}</div>
		<form class="hide" id="targetForm{$line->id}"  method="post" action="line/{$line->id}/text">
			<textarea name="text" class="line_text">{$line->revised_text}</textarea>
			<input type="submit" value="update">
			<input type="submit" value="cancel" name="cancel">
			<p>[original: {$line->text}]</p>
		</form>
		{else}
		<div class="line">{$line->text}</div>
		<form class="hide" id="targetForm{$line->id}"  method="post" action="line/{$line->id}/text">
			<textarea name="text" class="line_text">{$line->text}</textarea>
			<input type="submit" value="update">
			<input type="submit" value="cancel" name="cancel">
		</form>
		{/if}
		<div class="clear"></div>
		</li>
		{/foreach}
	</ul>
	{if $show_hidden}
	<a href="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}/lines">hide hidden lines</a>
	{else}
	<a href="faculty/{$fac->eid}/file/{$v->uploaded_file_id}/version/{$v->id}/lines?show_hidden=1">display hidden lines</a>
	{/if}
</div>

<div class="clear"></div>

{/block}
