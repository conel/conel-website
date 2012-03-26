<?php

/**
 * Two constants used to guess the path- and file-name of the page
 * when the user doesn't set any pther value
 */
define('CURRENT_FILENAME', basename($_SERVER['PHP_SELF']));
define('CURRENT_PATHNAME', str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])));

/**
 * class_html_pager - Generic data paging class  ("sliding window" style)
 *
 */
class Html_pager {
    /**
     * @var integer number of items
     * @access private
     */
    var $_totalItems;

    /**
     * @var integer number of items per page
     * @access private
     */
    var $_perPage     = 1;

    /**
     * @var integer number of page links before and after the current one
     * @access private
     */
    var $_delta       = 3;

    /**
     * @var integer current page number
     * @access private
     */
    var $_currentPage = 1;

    /**
     * @var string CSS class for links
     * @access private
     */
    var $_linkClass   = '';

    /**
     * @var string wrapper for CSS class name
     * @access private
     */
    var $_classString = '';

    /**
     * @var string path name
     * @access private
     */
    var $_path        = CURRENT_PATHNAME;

    /**
     * @var string file name
     * @access private
     */
    var $_fileName    = CURRENT_FILENAME;

    /**
     * @var string name of the querystring var for pageID
     * @access private
     */
    var $_urlVar      = '';

    /**
     * @var string name of the url without the pageID number
     * @access private
     */
    var $_url         = '';

    /**
     * @var string alt text for "previous page"
     * @access private
     */
    var $_altPrev     = 'previous page';

    /**
     * @var string alt text for "next page"
     * @access private
     */
    var $_altNext     = 'next page';

    /**
     * @var string alt text for "page"
     * @access private
     */
    var $_altPage     = 'page';

    /**
     * @var string image/text to use as "prev" link
     * @access private
     */
    var $_prevImg     = '';

    /**
     * @var string image/text to use as "next" link
     * @access private
     */
    var $_nextImg     = '';

    /**
     * @var string link separator
     * @access private
     */
    var $_separator   = '';

    /**
     * @var integer number of spaces before separator
     * @access private
     */
    var $_spacesBeforeSeparator = 1;

    /**
     * @var integer number of spaces after separator
     * @access private
     */
    var $_spacesAfterSeparator  = 1;

    /**
     * @var string CSS class name for current page link
     * @access private
     */
    var $_curPageLinkClassName  = '';

    /**
     * @var string Text before current page link
     * @access private
     */
    var $_curPageSpanPre        = '<strong>';

    /**
     * @var string Text after current page link
     * @access private
     */
    var $_curPageSpanPost       = '</strong>';

    /**
     * @var string Text before first page link
     * @access private
     */
    var $_firstPagePre  = '[&nbsp;';

    /**
     * @var string Text to be used for first page link
     * @access private
     */
    var $_firstPageText = '';

    /**
     * @var string Text after first page link
     * @access private
     */
    var $_firstPagePost = '&nbsp;]';

    /**
     * @var string Text before last page link
     * @access private
     */
    var $_lastPagePre   = '[&nbsp;';

    /**
     * @var string Text to be used for last page link
     * @access private
     */
    var $_lastPageText  = '';

    /**
     * @var string Text after last page link
     * @access private
     */
    var $_lastPagePost  = '&nbsp;]';

    /**
     * @var string Will contain the HTML code for the spaces
     * @access private
     */
    var $_spacesBefore  = '';

    /**
     * @var string Will contain the HTML code for the spaces
     * @access private
     */
    var $_spacesAfter   = '';

    /**
     * @var string Complete set of links
     * @access public
     */
    var $links = '';

    /**
     * @var array Array with a key => value pair representing
     *            page# => bool value (true if key==currentPageNumber).
     *            can be used for extreme customization.
     * @access public
     */
    var $range = array();


    /**
     * Constructor
     *
     * -------------------------------------------------------------------------
     * VALID options are (default values are set some lines before):
     *  - totalItems (int):    # of items to page.
     *  - perPage    (int):    # of items per page.
     *  - delta      (int):    # of page #s to show before and after the current
     *                         one
     *  - linkClass  (string): name of CSS class used for link styling.
     *  - path       (string): complete path to the page (without the page name)
     *  - fileName   (string): name of the page, with a %d if append=true
     *  - urlVar     (string): name of pageNumber URL var, for example "pageID"
     *  - altPrev    (string): alt text to display for prev page, on prev link.
     *  - altNext    (string): alt text to display for next page, on next link.
     *  - altPage    (string): alt text to display before the page number.
     *  - prevImg    (string): sth (it can be text such as "<< PREV" or an
     *                         <img/> as well...) to display instead of "<<".
     *  - nextImg    (string): same as prevImg, used for NEXT link, instead of
     *                         the default value, which is ">>".
     *  - separator  (string): what to use to separate numbers (can be an
     *                         <img/>, a comma, an hyphen, or whatever.
     *  - spacesBeforeSeparator
     *               (int):    number of spaces before the separator.
     *  - firstPagePre (string):
     *                         string used before first page number (can be an
     *                         <img/>, a "{", an empty string, or whatever.
     *  - firstPageText (string):
     *                         string used in place of first page number
     *  - firstPagePost (string):
     *                         string used after first page number (can be an
     *                         <img/>, a "}", an empty string, or whatever.
     *  - lastPagePre (string):
     *                         similar to firstPagePre.
     *  - lastPageText (string):
     *                         similar to firstPageText.
     *  - lastPagePost (string):
     *                         similar to firstPagePost.
     *  - spacesAfterSeparator
     *               (int):    number of spaces after the separator.
     *  - curPageLinkClassName
     *               (string): name of CSS class used for current page link.
     * -------------------------------------------------------------------------
     * REQUIRED options are:
     *  - fileName IF append==false (default is true)
     * -------------------------------------------------------------------------
     *
     * @param mixed $options    An associative array of option names and
     *                          their values.
     * @access public
     */
    function Html_pager($options = array()) {
        $this->_setOptions($options);
        $this->_generatePageData();
        $this->_setFirstLastText();

        if ($this->_totalPages > (2 * $this->_delta + 1)) {
            $this->links .= $this->_printFirstPage();
        }

        $this->links .= $this->_getBackLink();
        $this->links .= $this->_getPageLinks();
        $this->links .= $this->_getNextLink();

        if ($this->_totalPages > (2 * $this->_delta + 1)) {
            $this->links .= $this->_printLastPage();
        }
    }

    /**
     * Returns the start and end item for this page
     * e.g.: if you have 30 items, 5 per page and you are on
     * page number 3-> you will get first_item: 
     */
    function getPageItems($pageID = null) {
    	$current_page = isset($pageID) ? $pageID : $this->_currentPage;
    	$current_page --;
    	
    	$current_start  = ($this->_perPage * $current_page)+1;
    	$current_end	= ($current_start + $this->_perPage) -1;
    	
    	if($current_end > $this->_totalItems) {
    		$current_end = $this->_totalItems;
    	}
    	
        return array('start'=>$current_start,'end'=>$current_end);
    }


    /**
     * Returns ID of current page
     */
    function getCurrentPageID() {
        return $this->_currentPage;
    }

    // }}}
    // {{{ getNextPageID()

    /**
     * Returns next page ID. If current page is last page
     * this function returns FALSE
     *
     * @return mixed Next pages' ID
     * @access public
     */
    function getNextPageID() {
        return ($this->_currentPage == $this->_totalPages ? false : $this->_currentPage + 1);
    }

    /**
     * Returns previous page ID. If current page is first page
     * this function returns FALSE
     */
    function getPreviousPageID() {
        return $this->isFirstPage() ? false : $this->getCurrentPageID() - 1;
    }

    /**
     * Returns number of items
     */
    function numItems() {
        return $this->_totalItems;
    }

    /**
     * Returns number of pages
     */
    function numPages() {
        return (int)$this->_totalPages;
    }

    /**
     * Returns whether current page is first page
     */
    function isFirstPage() {
        return ($this->_currentPage == 1);
    }

    /**
     * Returns whether current page is last page
     */
    function isLastPage() {
        return ($this->_currentPage == $this->_totalPages);
    }

    /**
     * Returns whether last page is complete
     */
    function isLastPageComplete() {
        return !($this->_totalItems % $this->_perPage);
    }

    /**
     * Returns back/next/first/last and page links,
     * both as ordered and associative array.
     */
    function getLinks($pageID = null) {
        if ($pageID != null) {
            $_sav = $this->_currentPage;
            $this->_currentPage = $pageID;

            $this->links = '';
            if ($this->_totalPages > (2 * $this->_delta + 1)) {
                $this->links .= $this->_printFirstPage();
            }
            $this->links .= $this->_getBackLink();
            $this->links .= $this->_getPageLinks();
            $this->links .= $this->_getNextLink();
            if ($this->_totalPages > (2 * $this->_delta + 1)) {
                $this->links .= $this->_printLastPage();
            }
        }

        $back  = str_replace('&nbsp;', '', $this->_getBackLink());
        $next  = str_replace('&nbsp;', '', $this->_getNextLink());
        $pages = $this->_getPageLinks();
        $first = $this->_printFirstPage();
        $last  = $this->_printLastPage();
        $all   = $this->links;
        $total_pages = $this->_totalPages;
        $current_page = $this->_currentPage;
		$nextURL = $this->_url.$this->getNextPageID();
		$previousURL = $this->_url.$this->getPreviousPageID();
		
		$currentItemsStart = ($this->_currentPage * ($this->_perPage)) - ($this->_perPage -1);
		$currentItemsEnd = ($this->_currentPage * ($this->_perPage));
		if($currentItemsEnd > $this->_totalItems) {
			$currentItemsEnd = $this->_totalItems;
		}
		
        if ($pageID != null) {
            $this->_currentPage = $_sav;
        }

        return array('currentItemsEnd'=>$currentItemsEnd,'currentItemsStart'=>$currentItemsStart,'totalItems'=>$this->_totalItems,'previousURL'=>$previousURL,'nextURL'=>$nextURL,'CurrentPage'=>$current_page,'totalNumberOfPages'=>$total_pages,'back'  => $back,'pages' => $pages,'next'  => $next,'first' => $first,'last' => $last,'all' => $all);
    }

    /**
     * Returns pages link
     */
    function _getPageLinks() {
        $links = '';
        if ($this->_totalPages > (2 * $this->_delta + 1)) {
                if (($this->_totalPages - $this->_delta) <= $this->_currentPage) {
                    $_expansion_before = $this->_currentPage - ($this->_totalPages - $this->_delta);
                } else {
                    $_expansion_before = 0;
                }
                for ($i = $this->_currentPage - $this->_delta - $_expansion_before; $_expansion_before; $_expansion_before--, $i++) {
                    if (($i != $this->_currentPage + $this->_delta)){ // && ($i != $this->_totalPages - 1)) {
                        $_print_separator_flag = true;
                    } else {
                        $_print_separator_flag = false;
                    }

                    $this->range[$i] = false;
                    $links .= sprintf('<a href="%s" %s title="%s">%d</a>',
                                        ($this->_url.$i),
                                        $this->_classString,
                                        $this->_altPage.' '.$i,
                                        $i)
                           . $this->_spacesBefore
                           . ($_print_separator_flag ? $this->_separator.$this->_spacesAfter : '');
                }


            $_expansion_after = 0;
            for ($i = $this->_currentPage - $this->_delta; ($i <= $this->_currentPage + $this->_delta) && ($i <= $this->_totalPages); $i++) {
                if ($i<1) {
                    $_expansion_after++;
                    continue;
                }

                // check when to print separator
                if (($i != $this->_currentPage + $this->_delta) && ($i != $this->_totalPages )) {
                    $_print_separator_flag = true;
                } else {
                    $_print_separator_flag = false;
                }

                if ($i == $this->_currentPage) {
                    $this->range[$i] = true;
                    $links .= $this->_curPageSpanPre . $i . $this->_curPageSpanPost
                                 . $this->_spacesBefore
                                 . ($_print_separator_flag ? $this->_separator.$this->_spacesAfter : '');
                } else {
                    $this->range[$i] = false;
                    $links .= sprintf('<a href="%s" %s title="%s">%d</a>',
                                        ($this->_url.$i),
                                        $this->_classString,
                                        $this->_altPage.' '.$i,
                                        $i)
                                 . $this->_spacesBefore
                                 . ($_print_separator_flag ? $this->_separator.$this->_spacesAfter : '');
                }
            }

            if ($_expansion_after) {
                $links .= $this->_separator . $this->_spacesAfter;
                for ($i = $this->_currentPage + $this->_delta +1; $_expansion_after; $_expansion_after--, $i++) {
                    if (($_expansion_after != 1)) {
                       $_print_separator_flag = true;
                    } else {
                        $_print_separator_flag = false;
                    }

                    $this->range[$i] = false;
                    $links .= sprintf('<a href="%s" %s title="%s">%d</a>',
                                        ($this->_url.$i),
                                        $this->_classString,
                                        $this->_altPage.' '.$i,
                                        $i)
                           . $this->_spacesBefore
                           . ($_print_separator_flag ? $this->_separator.$this->_spacesAfter : '');
                }
            }

        } else {
            //if $this->_totalPages <= (2*Delta+1) show them all
            for ($i=1; $i<=$this->_totalPages; $i++) {
                if ($i != $this->_currentPage) {
                    $this->range[$i] = false;
                    $links .= sprintf('<a href="%s" %s title="%s">%d</a>',
                                    ($this->_url.$i),
                                    $this->_classString,
                                    $this->_altPage.' '.$i,
                                    $i);
                } else {
                    $this->range[$i] = true;
                    $links .= $this->_curPageSpanPre . $i . $this->_curPageSpanPost;
                }
                $links .= $this->_spacesBefore
                       . (($i != $this->_totalPages) ? $this->_separator.$this->_spacesAfter : '');
            }
        }

		//If there's only one page, don't display links
		if ($this->_totalPages < 2) $links = '';


        return $links;
    }

    /**
     * Returns back link
     */
    function _getBackLink() {
        if ($this->_currentPage > 1) {
            $back = sprintf('<a href="%s" %s title="%s">%s</a>',
                            ($this->_url.$this->getPreviousPageID()),
                            $this->_classString,
                            $this->_altPrev,
                            $this->_prevImg)
                  . $this->_spacesBefore . $this->_spacesAfter;
        } else {
            $back = '';
        }
        return $back;
    }

    /**
     * Returns next link
     */
    function _getNextLink() {
        if ($this->_currentPage < $this->_totalPages) {
            $next = $this->_spacesAfter
                . sprintf('<a href="%s" %s title="%s">%s</a>',
                            ($this->_url.$this->getNextPageID()),
                            $this->_classString,
                            $this->_altNext,
                            $this->_nextImg)
                 . $this->_spacesBefore . $this->_spacesAfter;
        } else {
            $next = '';
        }
        return $next;
    }

    /**
     * Print [1]
     *
     * @return string String with link to 1st page,
     *                or empty string if this is the 1st page.
     * @access private
     */
    function _printFirstPage() {
        if ($this->isFirstPage()) {
            return '';
        } else {
            return sprintf('<a href="%s" %s title="%s">%s%s%s</a>',
                            ($this->_url.'1'),
                            $this->_classString,
                            $this->_altPage.' 1',
                            $this->_firstPagePre,
                            $this->_firstPageText,
                            $this->_firstPagePost)
                 . $this->_spacesBefore . $this->_spacesAfter;

        }
    }

    /**
     * Print [numPages()]
     *
     * String with link to last page, or empty string if this is the 1st page.
     */
    function _printLastPage() {
        if ($this->isLastPage()) {
            return '';
        } else {
            return sprintf('<a href="%s" %s title="%s">%s%s%s</a>',($this->_url.$this->_totalPages),$this->_classString,$this->_altPage.' '.$this->_totalPages,$this->_lastPagePre, $this->_lastPageText,$this->_lastPagePost);
        }
    }

    /**
     * Calculates all page data
     *
     * @access private
     */
    function _generatePageData() {

        $this->_totalPages = ceil((float)$this->_totalItems / (float)$this->_perPage);
        $i = 1;

		$this->_pageData = array();


        //prevent URL manual modification
        $this->_currentPage = min($this->_currentPage, $this->_totalPages);

    }

    /**
     * sets the private _firstPageText, _lastPageText variables
     * based on whether they were set in the options
     */
    function _setFirstLastText()
    {
        if ($this->_firstPageText == '') {
            $this->_firstPageText = '1';
        }

        if ($this->_lastPageText == '') {
            $this->_lastPageText = $this->_totalPages;
        }
    }

    /**
     * Returns the correct link for the back/pages/next links
     */
    function _getLinksUrl() {
        // Sort out query string to prevent messy urls
        $querystring = array();
        $qs = array();

        if (!empty($_SERVER['QUERY_STRING'])) {
            $qs = explode('&', $_SERVER['QUERY_STRING']);
            for ($i=0, $cnt=count($qs); $i<$cnt; $i++) {
                list($name, $value) = explode('=', $qs[$i]);
                if ($name != $this->_urlVar) {
                    $qs[$name] = $value;
                }
                unset($qs[$i]);
            }
        }

        foreach ($qs as $name => $value) {
            $querystring[] = $name . '=' . $value;
        }

        return '?' . implode('&', $querystring) . (!empty($querystring) ? '&' : '') . $this->_urlVar .'=';
    }

    /**
     * Set and sanitize options
     *
     * @param mixed $options    An associative array of option names and
     *                          their values.
     * @access private
     */
    function _setOptions($options)
    {
        $allowed_options = array(
            'totalItems',
            'perPage',
            'delta',
            'linkClass',
            'path',
            'fileName',
            'urlVar',
            'altPrev',
            'altNext',
            'altPage',
            'prevImg',
            'nextImg',
            'separator',
            'spacesBeforeSeparator',
            'spacesAfterSeparator',
            'curPageLinkClassName',
            'firstPagePre',
            'firstPageText',
            'firstPagePost',
            'lastPagePre',
            'lastPageText',
            'lastPagePost'
        );

        foreach ($options as $key => $value) {
            if (in_array($key, $allowed_options) && ($value !== null)) {
                $this->{'_' . $key} = $value;
            }
        }

        $this->_fileName = ltrim($this->_fileName, '/');  //strip leading slash
        $this->_path     = rtrim($this->_path, '/');      //strip trailing slash

		$this->_fileName = CURRENT_FILENAME; //avoid easy-verified user error;
		$this->_url = $this->_path.'/'.$this->_fileName.$this->_getLinksUrl();

        if (strlen($this->_linkClass)) {
            $this->_classString = 'class="'.$this->_linkClass.'"';
        } else {
            $this->_classString = '';
        }

        if (strlen($this->_curPageLinkClassName)) {
            $this->_curPageSpanPre  = '<span class="'.$this->_curPageLinkClassName.'">';
            $this->_curPageSpanPost = '</span>';
        }

        if ($this->_perPage < 1) {   //avoid easy-verified user error
            $this->_perPage = 1;
        }

        for ($i=0; $i<$this->_spacesBeforeSeparator; $i++) {
            $this->_spacesBefore .= '&nbsp;';
        }

        for ($i=0; $i<$this->_spacesAfterSeparator; $i++) {
            $this->_spacesAfter .= '&nbsp;';
        }

        if (isset($_GET[$this->_urlVar])) {
            $this->_currentPage = max((int)@$_GET[$this->_urlVar], 1);
        } else {
            $this->_currentPage = 1;
        }
    }

    // }}}
}
?>