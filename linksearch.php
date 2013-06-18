<?php

// START OF SCRIPT

        $target_url = $_GET["urltocheck"];      // The page we search... 

        $linklist = array();
        $pieces = array();

        echo "<h2 align = center>Results for " . $target_url . "</h2>";


// This function is searching a URL for links

    function checkforlinks($request_url){

		    global $linklist;
		    global $basedomain;


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);    // The url to get links from
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // We want to get the respone
        $result = curl_exec($ch);

        $regex='|<a.*?href="(.*?)"|';
        preg_match_all($regex,$result,$parts);
        $links=$parts[1];

		    foreach($links as $link){

			      if((strpos($link, $request_url) !== false)||($link[0] != '/')) $link_url = $link;
				          else $link_url = $basedomain . $link;

  			$linklist[] = $link_url;

    }
		
    curl_close($ch);

}


	if(substr($target_url, -1, 1)=='/') {
		$link = substr_replace($target_url ,"",-1);
		$target_url = $link;
	}

	$pieces = explode("/", $target_url);

	$basedomain = $pieces[0] . "/" . $pieces[1] . "/" . $pieces[2];

	checkforlinks($target_url);


	foreach($linklist as $link){
		$count++;

		echo $count . ". ";

		if(strpos($link, $basedomain)!==false) {
			echo "<font color=green>" . $link ."</font>";
		}
		else {
			echo "<font color=red>" . $link . "</font>";
		}

		echo ' - <a target="_blank" href="' . $link . '"> Open</a><br>';

	}



// END OF SCRIPT
?>
