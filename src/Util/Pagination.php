<?php

/**
 * Class Pagination
 * @desc Helper class for paginating Slim View and Eloquent Pagination
 */
class Pagination
{
    const PAGE_NAME = 'page';
    const PER_PAGE = 10;
    const RENDER_TEMPLATE = 'pagination.twig';

    private $request;
    private $view;
    private $pageName;
    private $pageNameAfter;

    /**
     * Pagination constructor.
     * @param \Slim\Http\Request $request
     * @param \Slim\Views\Twig $view
     * @param string $pageName
     * @param null $pageNameAfter (e.g. for HTML anchors with #tag)
     */
    public function __construct(\Slim\Http\Request &$request, \Slim\Views\Twig &$view, $pageName = self::PAGE_NAME, $pageNameAfter = NULL)
    {
        $this->request = $request;
        $this->view = $view;
        $this->pageName = $pageName;
        $this->pageNameAfter = $pageNameAfter;
    }

    /**
     * Get current page from pageName parameter
     * @return int|mixed
     */
    public function resolveCurrentPage()
    {
        $page = $this->request->getParam($this->pageName, 1);

        if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
            return $page;
        }

        return 1;
    }

    /**
     * Fetches required information for rendering, used Eloquent Paginator. The array keys should match your view
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @return array
     */
    public function fetchPaginationDetails(\Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator)
    {
        return [
            'currentPage' => $paginator->currentPage(),
            'lastPage' => ceil($paginator->total()/$paginator->perPage()),
            'paginationPath' => $this->request->getUri()->getPath() . '?' . $this->pageName . '=',
            'pageName' => $this->pageName,
            'pageNameAfter' => $this->pageNameAfter
        ];
    }

    /**
     * @return string
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * Generate links with the help of the given $view
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param string $template
     * @return string (html)
     */
    public function links(\Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator, $template = self::RENDER_TEMPLATE)
    {
        return $this->view->fetch($template, $this->fetchPaginationDetails($paginator));
    }
}