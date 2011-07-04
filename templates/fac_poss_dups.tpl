{extends file="layout.tpl"}

{block name="content"}



<div class="controls" id="facinfo">
<a href="faculty/{$fac->eid}">{$fac->firstname} {$fac->lastname} faculty page</a>
</div>

<h2>{$fac->lastname}, {$fac->firstname} ({$fac->eid})</h2>
<div class="duplines section">
<h3>Lines with possible duplicates</h3>
<ul class="poss_dups" id="poss_dups">
	{foreach item=line from=$lines}
	{if $line->possible_dups|@count}
	<li>
	<div class="orig">{$line->text}</div>
	{foreach item=dupline from=$line->possible_dups}

	{if $dupline->is_asserted_dup_of == $line->id}

	<p class="dim">
	{$dupline->text}
	</p>
	<p>
	declared a duplicate (discard) {$dupline->dup_ago} by {$dupline->dup_asserted_by}
	<a class="delete" href="line/{$dupline->id}/assertions">[UNDO]</a>
	</p>

	{elseif $dupline->is_asserted_better_dup_of == $line->id}

	<p class="dim">
	{$dupline->text}
	</p>
	<p>
	declared a duplicate (save) {$dupline->dup_ago} by {$dupline->better_dup_asserted_by}
	<a class="delete" href="line/{$dupline->id}/assertions">[UNDO]</a>
	</p>

	{elseif $dupline->is_asserted_not_dup_of == $line->id}

	<p class="dim">
	{$dupline->text}
	</p>
	<p>
	declared NOT a duplicate {$dupline->nodup_ago} by {$dupline->not_dup_asserted_by}
	<a class="delete" href="line/{$dupline->id}/assertions">[UNDO]</a>
	</p>

	{elseif $dupline->is_not_cite}

	<p class="dim">
	{$dupline->text}
	</p>
	<p>
	declared NOT a citation 
	<a class="delete" href="line/{$dupline->id}/assertions">[UNDO]</a>
	</p>

	{else}

	<p>
	{$dupline->text}
	<p>
	</p>
	DUPLICATE: 
	<a class="dup" href="line/{$dupline->id}/better_dup_assertion/{$line->id}">SAVE</a> |
	<a class="dup" href="line/{$dupline->id}/dup_assertion/{$line->id}">DISCARD</a>
 	| 
	<a class="no_dup" href="line/{$dupline->id}/nodup_assertion/{$line->id}">NOT A DUPLICATE</a> |
	<a class="not_cite" href="line/{$dupline->id}/not_cite_assertion">NOT A CITATION</a> 
	<span class="lev">[levenshtein: {$dupline->levenshtein}]</span>
	</p>

	{/if}

	{/foreach}
	{/if}
	{/foreach}
</ul>

</div>
{/block}
