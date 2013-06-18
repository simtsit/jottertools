<?php
// START OF SCRIPT

        $time = microtime();            // These lines of code
        $time = explode(' ', $time);    // are for calculating
        $time = $time[1] + $time[0];    // the time this page
        $start = $time;                 // needs to be loaded.

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

        function crawl($request_url, $parent){

                global $seen;
                global $target_url;
		global $check_url;
                global $shownotes;
		global $count;
		global $checkfor404;
		global $showexternallinks;

		if($request_url[0] == '/')
			$request_url = $target_url . $request_url;


		if(array_search($request_url, $seen)){
                        if($shownotes) { echo "<font color=red>" . $request_url . " has been crawled earlier.</font></br>"; }
                        return;
		}

		if(strpos($request_url, $target_url)===false){
			if($shownotes) echo $parent . " --> " . $request_url . " is external! aborting!<br>";
			return;
		}


                if(strpos($request_url, "/downloadfile/")) {
			if($shownotes) { echo "<font color=red>" . $request_url . " aborting... it's a file.</font></br>"; }
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

			crawl($link, $request_url);

			}

                curl_close($ch);

        }




        crawl($target_url, "Menu");

      echo "<h2 align=center>Results for " . $target_url . "</h2><br><br>";

	

// END OF SCRIPT
?>
