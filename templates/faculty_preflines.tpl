{extends file="layout.tpl"}

{block name="content"}



<div class="controls">
	<a href="faculty/{$fac->eid}">return to {$fac->firstname} {$fac->lastname} faculty page</a>
</div>

<div id="lines">
	<p>lines last edited by: {$fac->lines_last_edited_by}</p>
<div class="section lines">
	<h2>Master ({$master->cv->orig_name})</h2>
	<ul class="overflow">
		{foreach item=line from=$master->lines}
		{if !$line->is_dup_of}
		<a name="line{$line->id}"></a>
		<li id="line{$line->id}" {if $line->is_hidden}class="hidden"{/if}  {if $line->is_creative}class="creative"{/if} {if $line->is_section}class="section"{/if}>
		<div class="operators">
			<p class="prob_code">
			{if $line->problem_note}{$line->problem_note}{/if}
			</p>
			{if $line->is_creative}
			<a href="line/{$line->id}/is_creative/0" class="put">[creative-]</a> 
			{else}
			<a href="line/{$line->id}/is_creative/1" class="put">[creative+]</a> 
			{/if}
			{if $line->is_hidden}
			<a href="line/{$line->id}/is_hidden/0" class="put">[unhide]</a> 
			{else}
			<a href="line/{$line->id}/is_hidden/1" class="put">[hide]</a> 
			{/if}
			<p>
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
			<p>
			<a href="#" id="toggleProblemForm{$line->id}" class="toggle">[edit problem flag]</a>
			</p>
			<p>
			<a href="line/{$line->id}/poss_dups.json" data-dupcount="{$line->poss_dups_count|default:0}" class="show_line_form" id="toggleForm{$line->id}">[view/edit]</a>
			</p>
			<!--
			<p>
			<a href="line/{$line->id}/test_parse.json">test parse</a>
			</p>
			-->
		</div>

		{if $line->revised_text}
		<div class="line">{$line->revised_text}
		<form class="hide" id="targetForm{$line->id}"  method="post" action="line/{$line->id}/text">
			<textarea name="text" class="line_text">{$line->revised_text}</textarea>
			<h4>{$line->poss_dups_count} possible duplicate(s)</h4>
			<ul><li>no duplicates</li></ul>
			<input type="submit" value="update">
			<input type="submit" value="cancel" name="cancel">
			<input type="submit" value="split into new line" name="split">
			<input type="submit" value="merge with previous" name="merge">
			<input type="submit" value="split into annotation" name="annotation">
				<p>
				<a href="#" data-content="{$fac->lastname}, {$fac->initial}." class="insert">prepend author w/ first initial</a> |
				<a href="#" data-content="{$fac->lastname}, {$fac->firstname}." class="insert">prepend author w/ first name</a>
				</p>
			<p>
			original:<br>{$line->text}
			{if $line->text != $line->revised_text}
			<a href="line/{$line->id}/selfdiff" class="op">[diff]</a>
			{/if}
			</p>
		</form>
		</div> 
		{else}
		<div class="line">{$line->text} 
		<form class="hide" id="targetForm{$line->id}"  method="post" action="line/{$line->id}/text">
			<textarea name="text" class="line_text">{$line->text}</textarea>
			<h4>{$line->poss_dups_count} possible duplicate(s)</h4>
			<ul><li>no duplicates</li></ul>
			<input type="submit" value="update">
			<input type="submit" value="cancel" name="cancel">
			<input type="submit" value="split into new line" name="split">
			<input type="submit" value="merge with previous" name="merge">
			<input type="submit" value="split into annotation" name="annotation">
				<p>
				<a href="#" data-content="{$fac->lastname}, {$fac->initial}." class="insert">prepend author w/ first initial</a> |
				<a href="#" data-content="{$fac->lastname}, {$fac->firstname}." class="insert">prepend author w/ first name</a>
				</p>
		</form>
		</div> 
		{/if}
		<div class="clear"></div>
		{if $line->annotation_text}
		<div class="line">{$line->annotation_text} <a href="#" id="toggleAnnotForm{$line->id}" class="toggle">[edit annotation]</a></div>
		<form class="hide" id="targetAnnotForm{$line->id}"  method="post" action="line/{$line->id}/annotation_text">
			<textarea name="annotation_text" class="line_text">{$line->annotation_text}</textarea>
			<input type="submit" value="update">
			<input type="submit" value="cancel" name="cancel">
		</form>
		{/if}

		<!-- problem codes -->
		<form class="hide" id="targetProblemForm{$line->id}" method="post" action="line/{$line->id}/problem">
			<p>
			<label>Problem Brief Description<span class="current">[{$line->problem_note}]</span></label>
			<input type="text" name="problem_note" value="{$line->problem_note}">
			<br>
			<select name="problem_code">
				<option>problem codes:</option>
				<option>PENDING</option>
				<option>AUTHOR_ORDER</option>
				<option>SUB_SUPER</option>
				<option>DERIVATIVE</option>
			</select>
			</p>
			<input type="submit" value="flag problem">
		</form>


		</li>
		{/if}
		{/foreach}
	</ul>
</div>
{foreach from=$pref_versions item=v}
<div class="section lines">
	<h2>From CV: {$v->cv->orig_name}</h2>
	<ul class="overflow">
		{foreach item=line from=$v->lines}
		{if !$line->is_dup_of}
		<a name="line{$line->id}"></a>
		<li id="line{$line->id}" {if $line->is_hidden}class="hidden"{/if}  {if $line->is_creative}class="creative"{/if} {if $line->is_section}class="section"{/if}>
		<div class="operators">
			<p class="prob_code">
			{if $line->problem_note}{$line->problem_note}{/if}
			</p>
			{if $line->is_creative}
			<a href="line/{$line->id}/is_creative/0" class="put">[creative-]</a> 
			{else}
			<a href="line/{$line->id}/is_creative/1" class="put">[creative+]</a> 
			{/if}
			{if $line->is_hidden}
			<a href="line/{$line->id}/is_hidden/0" class="put">[unhide]</a> 
			{else}
			<a href="line/{$line->id}/is_hidden/1" class="put">[hide]</a> 
			{/if}
			<p>
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
			<p>
			<a href="#" id="toggleProblemForm{$line->id}" class="toggle">[edit problem flag]</a>
			</p>
			<p>
			<a href="line/{$line->id}/poss_dups.json" data-dupcount="{$line->poss_dups_count|default:0}" class="show_line_form" id="toggleForm{$line->id}">[view/edit]</a>
			</p>
			<!--
			<p>
			<a href="line/{$line->id}/test_parse.json">test parse</a>
			</p>
			-->
		</div>

		{if $line->revised_text}
		<div class="line">{$line->revised_text}
		<form class="hide" id="targetForm{$line->id}"  method="post" action="line/{$line->id}/text">
			<textarea name="text" class="line_text">{$line->revised_text}</textarea>
			<h4>{$line->poss_dups_count} possible duplicate(s)</h4>
			<ul><li>no duplicates</li></ul>
			<input type="submit" value="update">
			<input type="submit" value="cancel" name="cancel">
			<input type="submit" value="split into new line" name="split">
			<input type="submit" value="merge with previous" name="merge">
			<input type="submit" value="split into annotation" name="annotation">
				<p>
				<a href="#" data-content="{$fac->lastname}, {$fac->initial}." class="insert">prepend author w/ first initial</a> |
				<a href="#" data-content="{$fac->lastname}, {$fac->firstname}." class="insert">prepend author w/ first name</a>
				</p>
			<p>
			original:<br>{$line->text}
			{if $line->text != $line->revised_text}
			<a href="line/{$line->id}/selfdiff" class="op">[diff]</a>
			{/if}
			</p>
		</form>
		</div> 
		{else}
		<div class="line">{$line->text} 
		<form class="hide" id="targetForm{$line->id}"  method="post" action="line/{$line->id}/text">
			<textarea name="text" class="line_text">{$line->text}</textarea>
			<h4>{$line->poss_dups_count} possible duplicate(s)</h4>
			<ul><li>no duplicates</li></ul>
			<input type="submit" value="update">
			<input type="submit" value="cancel" name="cancel">
			<input type="submit" value="split into new line" name="split">
			<input type="submit" value="merge with previous" name="merge">
			<input type="submit" value="split into annotation" name="annotation">
				<p>
				<a href="#" data-content="{$fac->lastname}, {$fac->initial}." class="insert">prepend author w/ first initial</a> |
				<a href="#" data-content="{$fac->lastname}, {$fac->firstname}." class="insert">prepend author w/ first name</a>
				</p>
		</form>
		</div> 
		{/if}
		<div class="clear"></div>
		{if $line->annotation_text}
		<div class="line">{$line->annotation_text} <a href="#" id="toggleAnnotForm{$line->id}" class="toggle">[edit annotation]</a></div>
		<form class="hide" id="targetAnnotForm{$line->id}"  method="post" action="line/{$line->id}/annotation_text">
			<textarea name="annotation_text" class="line_text">{$line->annotation_text}</textarea>
			<input type="submit" value="update">
			<input type="submit" value="cancel" name="cancel">
		</form>
		{/if}

		<!-- problem codes -->
		<form class="hide" id="targetProblemForm{$line->id}" method="post" action="line/{$line->id}/problem">
			<p>
			<label>Problem Brief Description<span class="current">[{$line->problem_note}]</span></label>
			<input type="text" name="problem_note" value="{$line->problem_note}">
			<br>
			<select name="problem_code">
				<option>problem codes:</option>
				<option>PENDING</option>
				<option>AUTHOR_ORDER</option>
				<option>SUB_SUPER</option>
				<option>DERIVATIVE</option>
			</select>
			</p>
			<input type="submit" value="flag problem">
		</form>


		</li>
		{/if}
		{/foreach}
	</ul>
	{if $show_hidden}
	<a href="faculty/{$fac->eid}/preflines">hide hidden lines</a>
	{else}
	<a href="faculty/{$fac->eid}/preflines?show_hidden=1">display hidden lines</a>
	{/if}
</div>
{/foreach}
</div>

<div class="clear"></div>

{/block}
