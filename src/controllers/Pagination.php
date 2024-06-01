<?php

declare(strict_types=1);

namespace Steamy\Controller;

/**
 * Controller for managing the pagination component
 */
class Pagination
{
    private int $items_per_page;
    private int $total_items;
    private int $current_page_number;

    public function __construct(int $items_per_page = 1, int $total_items = 1, int $current_page = 1)
    {
        $this->items_per_page = $items_per_page;
        $this->total_items = $total_items;
        $this->current_page_number = $current_page;
    }

    /**
     * Returns a query string that maintains all current query string parameters, except page number.
     * @return string Query string
     */
    private function getCurrentQueryString(): string
    {
        // create a string with all past query parameters except page and url
        unset($_GET['page']);
        unset($_GET['url']);

        return '?' . http_build_query($_GET);
    }

    /**
     * @param array $array
     * @return array New array containing only elements to be displayed on current page
     */
    public function getCurrentItems(array $array): array
    {
        return array_slice(
            $array,
            ($this->current_page_number - 1) * $this->items_per_page,
            $this->items_per_page
        );
    }

    /**
     * Returns HTML code need to display pagination items
     * @return string
     */
    public function getHTML(): string
    {
        $current_page_number = $this->current_page_number;
        $total_pages = (int)ceil((float)$this->total_items / $this->items_per_page);
        $query_string = $this->getCurrentQueryString();

        $view_file_path = __DIR__ . '/../views/Pagination.php';
        $html = '';

        // get content from view file
        ob_start();
        include $view_file_path;
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public function index(): void
    {
        // we don't want the page /pagination to be accessible
        (new Error())->handlePageNotFoundError();
    }
}
