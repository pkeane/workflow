{extends file="layout.tpl"}

{block name="content"}
<h1>Reports</h1>

<ul>
	<li>
	<a href="report/need_cv">list (TN,TT) Faculty without complete CV on file</a>
	|
	<a href="report/need_cv.csv">as CSV</a>
	</li>
	<li>
	<a href="report/need_cv?have=1">list (TN,TT) Faculty without ANY CV on file</a>
	|
	<a href="report/need_cv.csv?have=1">as CSV</a>
	</li>
	<li>
	<a href="report/null_status">list (TN,TT) Faculty with ANY CV status = NULL</a>
	|
	<a href="report/null_status.csv">as CSV</a>
	</li>
	<li>
	<a href="report/need_cv?noten=1">list (NT,RT,null) Faculty without complete CV on file</a>
	|
	<a href="report/need_cv.csv?noten=1">as CSV</a>
	</li>
	<li>
	<a href="report/need_cv?have=1&noten=1">list (NT,RT,null) Faculty without ANY CV on file</a>
	|
	<a href="report/need_cv.csv?have=1&noten=1">as CSV</a>
	</li>
	<li>
	<a href="report/null_status?noten=1">list (NT,RT,null) Faculty with ANY CV status = NULL</a>
	|
	<a href="report/null_status.csv?noten=1">as CSV</a>
	</li>
	<li>
	<a href="report/problem_files">list CVs with a reported problem</a>
	</li>
	<li>
	<a href="report/problem_faculty">list Faculty with a reported problem</a>
	</li>
</ul>
{/block}
