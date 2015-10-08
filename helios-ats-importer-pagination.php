<?php

/* Setup page vars for display. */
if ($paged == 0 || $paged < 0) {
    $paged = 1;  //if no page var is given, default to 1.
}
$prev = $paged - 1;       //previous page is page - 1
$next = $paged + 1;       //next page is page + 1
$lastpage = ceil($total_records / $limit);  //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;      //last page minus 1

$pagination = "";
if ($total_records > 0) {
    $pagination .= '<span class="displaying-num">' . $total_records . ' items </span>';
}
if ($lastpage > 1) {
    $pagination .= '<span class="pagination-links">';
    if ($paged > 1) {
        $pagination .= '<a href="' . $targetpage . $search_query . '" title="Go to the first page" class="first-page">«</a>';
        $pagination .= '<a href="' . $targetpage . $search_query . '&paged=' . $prev . '" title="Go to the previous page" class="prev-page">‹</a>';
    } else {
        $pagination .= '<a href="' . $targetpage . $search_query . '" title="Go to the first page" class="first-page disabled">«</a>';
        $pagination .= '<a href="' . $targetpage . $search_query . '&paged=1" title="Go to the previous page" class="prev-page disabled">‹</a>';
    }

    for ($counter = 1; $counter <= $lastpage; $counter++) {
        if ($counter == $paged)
            $pagination .= '<span class="paging-input"> ' . $paged . ' of <span class="total-pages">' . $lastpage . '</span></span>';
    }

    if ($paged == $lastpage) {
        $pagination .= '<a href="' . $targetpage . $search_query . '&paged=' . $lastpage . '" title="Go to the next page" class="next-page disabled">›</a>';
        $pagination .= '<a href="' . $targetpage . $search_query . '&paged=' . $lastpage . '" title="Go to the last page" class="next-page disabled">»</a>';
    } else {
        $pagination .= '<a href="' . $targetpage . $search_query . '&paged=' . $next . '" title="Go to the next page" class="next-page">›</a>';
        $pagination .= '<a href="' . $targetpage . $search_query . '&paged=' . $lastpage . '" title="Go to the last page" class="next-page">»</a>';
    }

    $pagination.= "</span>\n";
}
?>
