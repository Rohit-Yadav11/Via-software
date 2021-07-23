<?php
namespace WPDM\libs;

class Pagination{


                /*Default values*/

                var $total_pages = -1;//items

                var $limit = null;

                var $target = "";

                var $page = 1;

                var $adjacents = 2;

                var $showCounter = false;

                var $className = "pagination";

                var $parameterName = "page";

                var $urlF = false;//urlFriendly

                var $uriTPL = '';



                /*Buttons next and previous*/

                var $nextT = "Next";

                var $nextI = ""; //&#9658;

                var $prevT = "Previous";

                var $prevI = ""; //&#9668;



                /*****/

                var $calculate = false;

                var $nofollow = false;
                var $noindex = false;
                var $async = false;
                var $container = '#';
                var $showPageNumbers = true;


                function __construct(){
                    $this->prevT = __( "Previous" , "download-manager" );
                    $this->nextT = __( "Next" , "download-manager" );
                }




                #Total items

                function items($value){$this->total_pages = (int) $value;}


                function nofollow($bool){
                    $this->nofollow = $bool;
                }

                function noindex($bool){
                    $this->noindex = $bool;
                }

                function showPageNumbers($bool){
                    $this->showPageNumbers = $bool;
                }

                function async($set = false){
                    $this->async = $set;
                }

                function container($container = ''){
                    $this->container = $container;
                }

                #how many items to show per page

                function limit($value){$this->limit = (int) $value;}



                #Page to sent the page value

                function target($value){$this->target = $value;}



                function urlTemplate($value){$this->uriTPL = $value;}



                #Current page

                function currentPage($value){$this->page = (int) $value;}



                #How many adjacent pages should be shown on each side of the current page?

                function adjacents($value){$this->adjacents = (int) $value;}



                #show counter?

                function showCounter($value=""){$this->showCounter=($value===true)?true:false;}



                #to change the class name of the pagination div

                function changeClass($value=""){$this->className=$value;}



                function nextLabel($value){$this->nextT = $value;}

                function nextIcon($value){$this->nextI = $value;}

                function prevLabel($value){$this->prevT = $value;}

                function prevIcon($value){$this->prevI = $value;}



                #to change the class name of the pagination div

                function parameterName($value=""){$this->parameterName=$value;}



                #to change urlFriendly

                function urlFriendly($value="%"){

                                if(preg_match('^ *$',$value)){

                                                $this->urlF=false;

                                                return false;

                                        }

                                $this->urlF=$value;

                        }



                var $pagination;



                function pagination(){}

                function show(){
                    $async = $this->async?'async':'';

                    return wpdm_paginate_links( $this->total_pages, $this->limit, $this->page, 'cp', array('container' => $this->container, 'async' => (isset($this->async) && $this->async == 1 ? 1 : 0), 'next_text' => ' <i style="display: inline-block;width: 8px;height: 8px;border-right: 2px solid;border-top: 2px solid;transform: rotate(45deg);margin-left: -2px;margin-top: -2px;"></i> ', 'prev_text' => ' <i style="display: inline-block;width: 8px;height: 8px;border-right: 2px solid;border-bottom: 2px solid;transform: rotate(135deg);margin-left: 2px;margin-top: -2px;"></i> '));


                }

                function get_pagenum_link($id){

                                /*

                                if(strpos($this->target,'?')===false)

                                                if($this->urlF)

                                                                return str_replace($this->urlF,$id,$this->target);

                                                        else

                                                                return "$this->target?$this->parameterName=$id";

                                        else

                                                return "$this->target&$this->parameterName=$id";

                               */

                               return str_replace('[%PAGENO%]',$id,$this->uriTPL);

                        }



                function calculate(){

                                $this->pagination = "";

                                $this->calculate == true;

                                $nofollow = $this->nofollow?'nofollow':'';
                                $noindex = $this->noindex?'noindex ':'';
                                $rel = "rel = '{$noindex}{$nofollow}'";

                                $error = false;

                                $this->pagination = paginate_links(array(
                                     'format'             => $this->uriTPL,
                                     'total'              => $this->total_pages,
                                     'current'            => $this->page,
                                     'show_all'           => false,
                                     'end_size'           => 1,
                                     'mid_size'           => 2,
                                     'prev_next'          => true,
                                     'prev_text'          => $this->prevT,
                                     'next_text'          => $this->nextT,
                                     'type'               => 'plain',
                                     'add_args'           => false,
                                     'add_fragment'       => '',
                                     'before_page_number' => '',
                                     'after_page_number'  => ''
                                 ));


                                return true;

                        }

        }

