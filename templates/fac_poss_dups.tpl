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


	<p>
	{$dupline->text}
	<span class="lev">[levenshtein: {$dupline->levenshtein}]</span>
	<a href="line/{$dupline->id}/diff" class="diff_link">[diff]</a>
	</p>

	{/foreach}
	{else}
	<li>
	<div class="orig">{$line->text}</div>
	<p>has possible dup which is possible dup of another line (likely a dup w/ in master)</p>
	</li>
	{/if}
	{/foreach}
</ul>

</div>
{/block}
