
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link type="text/css" rel="stylesheet" href="new.css" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	  
	 <?php
	  #session_destroy();
	  session_start();
	  
	  //Need to open the file with verb and noun data
	  $verb_to_noun_file = "verb_to_noun.json";
	  if(file_exists($verb_to_noun_file)) {
		 $file = file_get_contents($verb_to_noun_file, FILE_USE_INCLUDE_PATH);
		 $verb_to_noun = json_decode($file, true);                
	  } else {
		 print("Error");	 
	  };
	  
	  //Need to open the file with noun valency data
	  $noun_valency_file = "nouns.json";
	  if(file_exists($noun_valency_file)) {
		 $file = file_get_contents($noun_valency_file, FILE_USE_INCLUDE_PATH);
		 $noun_valency = json_decode($file, true);                
	  } else {
		 print("Error");	 
	  };       

	$reload = false;
	// setting up the verb session
	if (isset($_GET['verb'])) {$verb = $_GET['verb'];}
	else {
		$verb = "mit"; 
		$reload = true;
	}
	// setting up the noun session
	if  (isset($_GET['noun'])) {$noun = $_GET['noun'];}
	else {
		$nouns = $verb_to_noun[$verb]["nouns"]; 
		reset($nouns);
		$noun = $nouns[key($nouns)];	
		$reload = true;
	}
	// setting up the frame session
	if (isset($_GET['frame'])) {$_SESSION["frame"] = $_GET['frame'];}
	else {
		$frames = $verb_to_noun[$verb]["frames"]; 
		reset($frames);
		$_SESSION["frame"] = $frames[key($frames)];
		$verb_valency = $verb_to_noun[$verb]["valency"]; 
		$reload = true;
	}
	if ($reload) {
		header("Location:index.php?noun={$noun}&verb={$verb}&frame={$_SESSION["frame"]}");
		die();
	}
		
	//Getting the valency data for noun in PDT-Vallex
	if (isset($noun_valency[$noun])) {
		$noun_data = $noun_valency[$noun];
	} else {
		$noun_data = Null;
	}
	
	//Getting the results
	 $filename = "results.json";

	if(file_exists($filename)) {
		$file = file_get_contents($filename, FILE_USE_INCLUDE_PATH);
		$data = json_decode($file, true);                
	} else {
		file_put_contents($filename, json_encode(array()), LOCK_EX) or print("File writing failed.");	 
	}
	
	if (isset($data[$verb][$noun])) {
		$results = $data[$verb][$noun];
	} else {
		$results = array();
	}

	//Changing the frame, i.e. upper part of the screen
	$frames = $verb_to_noun[$verb]["frames"]; 
	//if (isset($_GET['frame'])) {
		 //$frame = $_GET['frame'];
	//};
	$verb_valency = $verb_to_noun[$verb]["valency"]; 
	$nouns = $verb_to_noun[$verb]["nouns"];
	?>
	 
	  
	  
</head>
<body>
	<div class = "side" <?php print "onScroll=\"document.cookie='ypos=' + window.pageYOffset\" onLoad='window.scrollTo(0,$ypos)'";?>>
		<?php include 'sidebar.html'; ?>
	</div>
	<div class = "downside">
		<?php foreach($nouns as $n) {
			print "<p><a href='index.php?noun={$n}&verb={$verb}&frame={$_SESSION["frame"]}'>{$n}</a></p>";
			}
		?>
	</div>	
	<div class = "header">  
		<?php 
			foreach ($frames as $key => $val) {
				print "<a href=\"index.php?frame={$val}&noun={$noun}&verb={$verb}
				\">{$key}</a>&nbsp;&nbsp;&nbsp;&nbsp;";
			};
		?>
	</div>

	<div class="upper">
		<object type="text/html" data="<?php echo "http://ufal.mff.cuni.cz/vallex/2.6.3/data/html/generated/lexeme-entries/{$_SESSION["frame"]}.html"?>"
			style="width:100%;height:100%; margin:0%;" >
		</object>
	</div>
	
	<div class="lower">

		<!--lemma-->
		<h2><?php echo "{$noun}"; ?></h2><br>
		<!--choosing the verb valency frame-->
		<form action="save.php?<?php echo "noun={$noun}&verb={$verb}&frame={$_SESSION["frame"]}"?>" method="post">
		<p style='text-align: center;'>Valency frame<br><select name="valency">
			<?php foreach($verb_valency as $val) {
				print  "  <option value=\"{$val}\"";
				if ($results["valency"] == $val ) {
					print "selected=\"selected\"";
				}
				print ">{$val}</option>";
			}
			?>
		</select></p>
		<!--Mapping attribute-->
		<p style='text-align: center;'>Mapping attribute<br>
		<?php print "<textarea name=\"mapping\">";
			if (array_key_exists("mapping",$results)) {print_r($results["mapping"]);} 
		print "</textarea>";
		?> 
		</p>
		<!--Instigator attribute-->
		<p style='text-align: center;'>Instigator attribute<br>
		<?php print "<textarea name=\"instigator\">";
			if (array_key_exists("instigator",$results)) {print_r($results["instigator"]);} 
		print "</textarea>";
		?> 
		</p>
		
		<!--valency_frames_of_noun-->
		<p><?php 
			print '<p style="text-align: center"><b>Noun valency:</b><br></p>';
			if (isset($noun_data)) {
			foreach ($noun_data as $key => $val) {
				print "<input type=\"checkbox\" name=\"{$key}_frame\" value=\"{$key}\"";
				if ($results["{$key}_frame"] == $key ) {print " checked=\"checked\"";}
				print "/>";
				print "{$val["elements"]}<br>";
				print "<div class=\"example\">{$val["examples"]}</div><br>";
				print "Note: <textarea name=\"{$key}_note\">";
				if (array_key_exists("{$key}_note",$results)) {print_r($results["{$key}_note"]);} 
				print"</textarea><br><br>";
				
			}
		} else {
			print "Note: <textarea name=\"noval_note\">";
			if (array_key_exists("noval_note",$results)) {print_r($results["noval_note"]);} 
			print"</textarea><br><br>";
			}
		?>
		<!--submit-->
		<p style='text-align: center;'><input type="submit" value="Send"/></p>
		</form>
	</div>
</body>
</html>
