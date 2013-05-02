<?php

function get_html_searchit($location, $query='')
{
    return<<<EOHTML
<div class="pg pg_bottom search">
<form id="searchit" method="get" action="/menu/search">
    <label><span class="hint">Look for 'fish tacos' or 'Japanese'</span>
        <input class="watermark query" type="text" name="query" placeholder="Search" value="${query}"/>
    </label>
    <label><span class="hint">Location</span>
        <input class="watermark location" type="text" name="location" placeholder="Location" value="${location}"/>
    </label>
    <label><span class="hint">&nbsp;</span>
        <button class="button search" type="submit">Search</button>
    </label>
</form>
</div>
EOHTML;
}
