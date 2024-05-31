<?php

declare(strict_types=1);

/***
 * The following variables have been defined in the Pagination controller.
 * @var int $current_page_number Current page number.
 * @var int $total_pages Total number of pages
 * @var string $query_string Query string for current page
 */

/**
 * Prints page item in HTML format.
 *
 * @param int $current_page_number
 * @param int $page_number Page number of page item
 * @param string $query_string
 * @return void
 */
function displayPageItem(int $current_page_number, int $page_number, string $query_string): void
{
    $page_link = $query_string . "&page=$page_number";
    $className = "page-item" . ($page_number === $current_page_number ? " active" : "");

    echo <<< EOL
    <li class="$className">
        <a class="page-link" href="$page_link">$page_number</a>
    </li>
    EOL;
}

/**
 * Prints navigation button in HTML format
 * @param int $current_page_number
 * @param int $total_pages Total number of pages
 * @param string $query_string
 * @param bool $is_left True indicates left navigation button.
 * @return void
 */
function displayNavigationButton(int $current_page_number, int $total_pages, string $query_string, bool $is_left): void
{
    $page_number = $current_page_number + ($is_left ? -1 : 1);
    $page_link = $query_string . "&page=$page_number";

    $link_content = htmlspecialchars($is_left ? "<" : ">");
    $className = "page-item";

    if (($current_page_number === 1 && $is_left) || ($current_page_number === $total_pages && !$is_left)) {
        $className .= " disabled";
    }

    echo <<< EOL
    <li class="$className">
        <a class="page-link" href="$page_link">$link_content</a>
    </li>
    EOL;
}

?>

<style>
  .pagination {
    display: flex;
    list-style: none;
    border-radius: 0.25rem;
    gap: 0.45rem;
    margin-top: 2cm;
  }


  .page-item {
    --bs-padding-x: 0.5rem;
    --bs-padding-y: 0.25rem;
  }

  .page-link {
    position: relative;
    display: block;
    padding: var(--bs-padding-y) var(--bs-padding-x);
    text-decoration: none;
    transition: color .25s ease-in-out, background-color .25s ease-in-out;
    outline: 1px solid #dee2e6;
  }

  .page-link:hover {
    z-index: 2;
    background-color: var(--contrast-hover);
    color: var(--contrast-inverse);
  }

  .page-link:focus {
    z-index: 3;
    outline: 0;
    box-shadow: 0 0 0.25rem rgba(0, 0, 0, 0.25);
  }

  .page-item.active .page-link {
    z-index: 3;
    background-color: var(--contrast);
    color: var(--contrast-inverse);
  }

  .page-item.disabled .page-link {
    color: var(--form-element-disabled-opacity);
    outline-color: var(--form-element-disabled-border-color);
    pointer-events: none;
    background-color: var(--form-element-disabled-background-color);
  }
</style>

<nav class="container" style="display: flex; justify-content: center">
    <ul class="pagination">
        <?php
        // Display previous page button
        displayNavigationButton(
            $current_page_number,
            $total_pages,
            $query_string,
            true
        );

        // Display each page item
        for ($page_num = 1; $page_num <= $total_pages; $page_num++) {
            displayPageItem($current_page_number, $page_num, $query_string);
        }

        // Display next page button
        displayNavigationButton(
            $current_page_number,
            $total_pages,
            $query_string,
            false
        );
        ?>
    </ul>
</nav>