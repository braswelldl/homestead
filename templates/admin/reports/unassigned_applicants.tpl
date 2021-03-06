<h2>Unassigned Applicants - {TERM}</h2>
<!-- BEGIN empty_results -->
{EMPTY_RESULTS}No unassigned applicants were found.
<!-- END empty_results -->

<table>
    <tr>
        <th>Banner ID</th>
        <th>{USERNAME_SORT}</th>
        <th>{GENDER_SORT}</th>
        <th>{STUDENT_TYPE_SORT}</th>
        <th>{APPLICATION_TERM_SORT}
        <th>Meal Plan</th>

        <!-- BEGIN summer_headers -->
        <th>{ROOM_TYPE_SORT}</th>
        <!-- END summer_headers -->

        <!-- BEGIN fall_headers -->
        <th>{LIFESTYLE_OPTION_SORT}</th>
        <th>{PREFERRED_BEDTIME_SORT}</th>
        <th>Room Condition</th>
        <th>Roommate</th>
        <!-- END fall_headers -->

        <th>Actions</th>

    </tr>
    <!-- BEGIN empty_table -->
    <tr>
        <td colspan="5">{EMPTY_MESSAGE}</td>
    </tr>
    <!-- END empty_table -->
    <!-- BEGIN listrows -->
    <tr>
        <td>{BANNER_ID}</td>
        <td>{USERNAME}</td>
        <td>{GENDER}</td>
        <td>{STUDENT_TYPE}</td>
        <td>{APP_TERM}</td>
        <td>{MEAL}</td>
        <!-- BEGIN summer_app -->
        <td>{ROOM_TYPE}</td>
        <!-- END summer_app -->
        <!-- BEGIN fall_app -->
        <td>{LIFESTYLE_OPTION}</td>
        <td>{PREFERRED_BEDTIME}</td>
        <td>{ROOM_CONDITION}</td>
        <!-- END fall_app -->
        <td>{ROOMMATE}</td>
        <td>{ACTIONS}</td>
    </tr>
    <!-- END listrows -->
</table>

<div class="align-center">
    {TOTAL_ROWS}<br />
    {PAGE_LABEL} {PAGES}<br />
    {LIMIT_LABEL} {LIMITS}<br />
    {CSV_REPORT}
</div>
