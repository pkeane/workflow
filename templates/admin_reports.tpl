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
	<a href="report/problem_faculty">list Faculty with a reported problem</a> |
	<a href="report/problem_faculty.csv">as CSV</a>
	</li>
	<li>
	<a href="report/noprob_faculty">list Faculty with NO reported problem</a> |
	<a href="report/noprob_faculty.csv">as CSV</a>
	</li>
	<li>
	<a href="report/problem_files">list CVs with a reported problem</a>
	</li>
	<li>
	<a href="report/pref_cv_multiple_versions">list preferred CVs with multiple versions</a>
	</li>
	<li>
	<a href="report/misassigned_files">list CVs assigned to wrong EID</a>
	</li>
	<li>
	<a href="report/faculty_poss_dups">list Faculty with possible duplicates</a>
	<!--
	<a href="report/noprob_faculty.csv">as CSV</a>
	-->
	</li>
	<li>
	<a href="report/faculty_no_pref_versions">list Faculty with NO preferred versions</a>
	</li>
	<li>
	<a href="report/faculty_has_pref_versions">list Faculty with AT LEAST ONE preferred version</a>
	</li>
	<li>
	<a href="report/recent_uploads">list recently uploaded CVs</a>
	</li>
</ul>
{/block}
