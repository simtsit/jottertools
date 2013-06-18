
<?php
// START OF SCRIPT

        $target_url = $_GET["urltocheck"];      // The page we search... 
        $check_url = $_GET["checkforlinks"];    // The links we're looking for...

        $result_options = $_GET["option"];

        if(in_array("shownotes", $result_options)) { $shownotes = 1; }
  if(in_array("showpages", $result_options)) { $showpages = 1; }


        if(in_array("checkforlinks", $result_options)) { $checkforlinks = 1; $linksurl = $_GET['linksurl']; }
        if(in_array("checkforphotos", $result_options)) { $checkforphotos = 1; $photosurl = $_GET['photosurl']; }

	if(in_array("showexternallinks", $result_options)) {$showexternallinks = 1; }
        if(in_array("checkfor404", $result_options)) { $checkfor404 = 1; }


        $seen = array();
	$count = 1;
	$photoscount = 1;


 // This function is looking for external links.

        function crawl($request_url){

                global $seen;
                global $target_url;
            		global $check_url;
                global $shownotes;
            		global $count;
	            	global $checkfor404;
	            	global $showexternallinks;

                if(strpos($request_url, "/downloadfile/")) {
			if($shownotes) { echo "<font color=red>" . $request_url . " aborting... it's a file.</font></br>"; }
			return;
                }

                if(strpos($request_url, "/sampled/")) {
                        if($shownotes) { echo "<font color=red>" . $request_url . " aborting... it's a file.</font></br>"; }
                        return;
                }

		$depth = explode("/",$request_url);

		if(sizeof($depth)>=8){
			if($shownotes) { echo "<font color=red>" . $request_url . " aborting... url too big.</font></br>"; }
			return;
		}

                if(array_search($request_url, $seen)){
                        if($shownotes) { echo "<font color=red>" . $request_url . " has been crawled earlier.</font></br>"; }
                        return;
                }
	

                $seen[] = $request_url;
                if($shownotes) { echo "<br><font color=green>Crawling " . $request_url. "...</font><br>"; }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $request_url);    // The url to get links from
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // We want to get the respone
                $result = curl_exec($ch);

                $regex='|<a.*?href="(.*?)"|';
		preg_match_all($regex,$result,$parts);
                $links=$parts[1];
	
                foreach($links as $link){

                        if ($link[0] == '/')
                                $link = $target_url . $link;


	                if($shownotes) { echo "<font color=blue>" . $link."</font> has been found in <font color=orange>" . $request_url ." </font><br>"; }

                        if (strpos($link, $target_url)!==false){                // if the link is internal...

				if(strpos($link,"/downloadfile/")) return;

                                if (array_search($link, $seen)) {
                                        if($shownotes) { echo "<font color=red>" . $link . " has been crawled earlier.</font></br>"; }
                                }
                                else {

			                if($checkfor404) { checkfor404($request_url, $link); }
                                        crawl($link);
                               }
                        } else {  //if the link is external
				if($showexternallinks) {
					if (($link != "http://www.schooljotter.com") || ($link != "http://www.webanywhere.com")) {
						echo $request_url . " - <font color=purple>" . $link ."</font><br>"; 
					}
				}
			}


                        if(strpos($link, $check_url) !== false) # && ($link != $check_url))
                        {

				if(($link == $check_url) && ($exception)) {

					echo "<font color=black>" . $request_url . "</font> - <font color=red>". $link . "</font></span><br><br>";
                                	$count += 1;
				}

				if($link != $check_url) {

					echo " <font color=black>" . $request_url . "</font> - <font color=red>". $link . "</font></span><br><br>";
                                        $count += 1;
                                } 

                        }
                }
                curl_close($ch);

        }

        crawl($target_url);

	echo "<br><br>";

        $count = 0;

	if ($showpages) {

	        echo "<h2 align = center>Pages found for " . $target_url . "</h2>";

                foreach ($seen as $subpage){
				$count++;
				echo $count . ". " . $subpage . "<br>";
                }
	}


      echo "<h2 align=center>Results for " . $target_url . "</h2><br><br>";

	

	$linkscount = 0;

        function checkforlinks($request_url, $linksurl){

                global $linkscount;
                global $target_url;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $request_url);    // The url to get links from
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // We want to get the respone
                $result = curl_exec($ch);

                $regex='|<a.*?href="(.*?)"|';
                preg_match_all($regex,$result,$parts);
                $links=$parts[1];

                foreach($links as $link){
                        if(strpos($link, $linksurl) !== false) {        // if the link is external...

                                if($link != $linksurl) {
                                        $linkscount += 1;
                                        echo $request_url . " - <font color=red>". $link . "</font><br>";
                                }

                        } #else checkfor404($request_url, $link);

                }
                curl_close($ch);
	}









	$photoscount = 0;

        function checkforphotos($request_url, $photosurl){

                global $photoscount;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $request_url);    // The url to get links from
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // We want to get the respone
                $result = curl_exec($ch);

                $regex='|<img.*?src="(.*?)"|';
                preg_match_all($regex,$result,$parts);
                $photos=$parts[1];

                foreach($photos as $photo){
                        if (strpos($photo, $photosurl)) {
                                $photoscount += 1;
                                echo $request_url . " - <font color=blue>".$photo ."</font><br>";
                        }
                }

                curl_close($ch);
        }





        function checkfor404($request_url, $link){

                global $pagenotfoundcount;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $link);    // The url to get links from
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // We want to get the respone
                $result = curl_exec($ch);

                $regex='/<title>(.+)<\/title>/i';
                preg_match_all($regex,$result,$parts);
                $titles=$parts[1];

                foreach($titles as $title){
                        if($title == "Page not found") {
                               $pagenotfoundcount += 1;
                               echo $request_url . " - <font color=green> " . $link . "</font><br>";
                        }
                curl_close($ch);
                }
        }






	foreach($seen as $page){
                if($checkforlinks) checkforlinks($page, $linksurl);
                if($checkforphotos) checkforphotos($page, $photosurl);

        }

        echo "<br><br>";
        if($checkforlinks) echo "There were totaly <font color=red>" . $linkscount . "</font> links found.<br>";
        if($checkforphotos) echo "There were totaly <font color=blue>" . $photoscount . "</font> photos found.<br>";
        if($checkfor404)  echo "There were totaly <font color=green>" . $pagenotfoundcount . "</font> broken pages found.</br>";



// END OF SCRIPT
?>
