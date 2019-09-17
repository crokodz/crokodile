<?php
/**************************************************************************************
 * Class: Pager
 * Author: Tsigo <tsigo@tsiris.com>
 * Methods:
 *         findStart
 *         findPages
 *         pageList
 *         nextPrev
 * Redistribute as you see fit.
 **************************************************************************************/
 class Pager
  {
  /***********************************************************************************
   * int findStart (int limit)
   * Returns the start offset based on $_GET['page'] and $limit
   ***********************************************************************************/
   function findStart($limit)
    {
     if ((!isset($_GET['page'])) || ($_GET['page'] == "1"))
      {
       $start = 0;
       $_GET['page'] = 1;
      }
     else
      {
       $start = ($_GET['page']-1) * $limit;
      }

     return $start;
    }
  /***********************************************************************************
   * int findPages (int count, int limit)
   * Returns the number of pages needed based on a count and a limit
   ***********************************************************************************/
   function findPages($count, $limit)
    {
     $pages = (($count % $limit) == 0) ? $count / $limit : floor($count / $limit) + 1;

     return $pages;
    }
  /***********************************************************************************
   * string pageList (int curpage, int pages)
   * Returns a list of pages in the format of "« < [pages] > »"
   ***********************************************************************************/
   function pageList($curpage, $pages,$self)
    {
     $_SERVER['PHP_SELF'] = $self;
     $page_list  = "";

     /* Print the first and previous page links if necessary */
     if (($curpage != 1) && ($curpage))
      {
       $page_list .= "  <a href=\"".$_SERVER['PHP_SELF']."&page=1\" title=\"First Page\">first</a> ";
      }
     else{
       $page_list .= " first ";
      }
     if (($curpage-1) > 0)
      {
       $page_list .= "<a href=\"".$_SERVER['PHP_SELF']."&page=".($curpage-1)."\" title=\"Previous Page\">prev</a> ";
      }
     else{
       $page_list .= " prev ";
      }

     /* Print the numeric page list; make the current page unlinked and bold */
     for ($i=0+$curpage; $i<=$curpage; $i++)
      {
       if ($i == $curpage)
        {
         $page_list .= "<b>".$i."</b>";
        }
       else
        {
         $page_list .= "<a href=\"".$_SERVER['PHP_SELF']."&page=".$i."\" title=\"Page ".$i."\">".$i."</a>";
        }
       $page_list .= " ";
      }

     /* Print the Next and Last page links if necessary */
     if (($curpage+1) <= $pages)
      {
       $page_list .= "<a href=\"".$_SERVER['PHP_SELF']."&page=".($curpage+1)."\" title=\"Next Page\">next</a> ";
      }
     else{
       $page_list .= " next ";
      }
     if (($curpage != $pages) && ($pages != 0))
      {
       $page_list .= "<a href=\"".$_SERVER['PHP_SELF']."&page=".$pages."\" title=\"Last Page\">last</a> ";
      }
     else{
       $page_list .= " last ";
      }
     $page_list .= "</td>\n";

     return $page_list;
    }
  /***********************************************************************************
   * string nextPrev (int curpage, int pages)
   * Returns "Previous | Next" string for individual pagination (it's a word!)
   ***********************************************************************************/
   function nextPrev($curpage, $pages,$self)
    {
    $_SERVER['PHP_SELF'] = $self;
     $next_prev  = "";

     if (($curpage-1) <= 0)
      {
       $next_prev .= "Previous";
      }
     else
      {
       $next_prev .= "<a href=\"".$_SERVER['PHP_SELF']."&page=".($curpage-1)."\">Previous</a>";
      }

     $next_prev .= " | ";

     if (($curpage+1) > $pages)
      {
       $next_prev .= "Next";
      }
     else
      {
       $next_prev .= "<a href=\"".$_SERVER['PHP_SELF']."&page=".($curpage+1)."\">Next</a>";
      }

     return $next_prev;
    }
  }
?>