<!--
/*  Collector (Garcia, Kornell, Kerr, Blake & Haffey)
    A program for running experiments on the web
    Copyright 2012-2016 Mikey Garcia & Nate Kornell


    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 3 as published by
    the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
 		
		Kitten release (2019) author: Dr. Anthony Haffey (a.haffey@reading.ac.uk)		
*/
-->

<style>
.change_tip{
	margin:2px;
}
.general_tip{
	display:none;
	margin:2px;
	padding:20px;
}
#help_content{
	position:fixed;
	right:0px;
	top:60px;
	bottom:0;
	overflow-y:auto;
}
</style>
<?php 

$cond_help = file_get_contents("IndexTabs/Help/CondHelp.txt");
$grap_help = file_get_contents("IndexTabs/Help/GrapHelp.txt");
$main_help = file_get_contents("IndexTabs/Help/MainHelp.txt");
$proc_help = file_get_contents("IndexTabs/Help/ProcHelp.txt");
$surv_help = file_get_contents("IndexTabs/Help/SurvHelp.txt");

?>

<div id="help_content" class="card help_class">
	<div class="card-body">
		<h5 class="card-title text-primary" id="help_title">Click on a something editable for help</h5>
		<h6 class="card-subtitle mb-2 text-muted" id="help_subtitle">or Click on "Help" to hide</h6>
		<p class="card-text" id="help_text">This text will change depending on where you click. Have fun!</p>
		
		<h5 class="card-title text-primary">General tips</h5>
		<p class="card-text help_general"></p>
		<button class="btn btn-primary change_tip">Previous</button>
		<button class="btn btn-primary change_tip">Next</button>
		
	</div>
</div>

<script>


help_obj = {
	cond:    <?= $cond_help ?>,
	graphic: <?= $grap_help ?>,
	main:    <?= $main_help ?>,
	proc:    <?= $proc_help ?>,
	surv:    <?= $surv_help ?>,
};

help_general_html = '<div class="container">';
help_obj.main.forEach(function(tip,index){
	help_general_html +=  '<div class="jumbotron general_tip tip'+index+'">' +
													tip +
												'</div>';
});
$(".help_general").html(help_general_html);

help_obj.tip_no = Math.floor(Math.random() * help_obj.main.length);
$(".tip"+help_obj.tip_no).show();


function helperActivate(help_title, cellValue,help_type){	
	$("#help_title").html(help_title);
  var help_title = help_title.toLowerCase();	
  var stim_sheets = $('#stim_select > option').map(function() { return this.value; }).get();
  var proc_sheets = $('#proc_select > option').map(function() { return this.value; }).get();
	
  //Graphic editor advice
  ///////////////////////
  if(help_type == "delay"){
    $("#help_subtitle").html("Autostart or not");
    $("#help_text").html("You can specify in (ms) the delay from the start of the trial you want this stimulus to play. If you want it to play immediately, then write in 0 into this input. If you do not want it to play automatically (e.g. if you want it to play when you click on another element), leave this input blank."); 
  }
  
	// give trialtype code advice
	/////////////////////////////
	if(help_type == "trialtype_code"){
		$("#help_subtitle").html("General tips");
    $("#help_text").html("Whilst Collector is written to try to make the trialtypes as generalisable as possible (in case you want to use them on another platform), there are a few functions that are specific to Collector: <br><br>" +
		"<b>Trial.set_timer(this_function,timeout)</b>: should be used if you want to set a timer from the beginning of a trial. This is because trials are buffered, and so <b>setTimeout</b> would be triggered from when the trial is buffered, not when the participant has started the trial. <br>Trial.set_timer requires two inputs, a function and then the time until it should be triggered. For example <br><br>" +
		"<em>Trial.set_timer(function(){ <br> alert('hi') <br>},5000)</em> <br><br>" +
		"<b>Trial.submit())</b>: ends the current trial. <br><br>"+
		"<b>Trial.elapsed()</b>: will return the time since the trial began. This is necessary due to buffering, as normal efforts to get the time since the trial began (e.g. using <em>(new Date()).getTime()</em>) may be distorted by the buffering of trials.");
	}
	
	// give keyboard advice
	///////////////////////
	if(help_type == "keyboard"){
		$("#help_subtitle").html("Response Keys");
    $("#help_text").html("Which keys do you want the participant to be able to respond with? Note that if you turn off the <b>End trial on key press</b> option the trial will not end when the participant presses one of the valid keys. However, the only key response that will be stored is the most recent one before the trial ended.");
	}
	
  // give Snippet advice
  //////////////////////
  if(help_type == "snippet"){
    $("#help_subtitle").html("General Advice");
    $("#help_text").html("Snippets are useful bits of code to use in surveys. <br><br>To preview a snippet either click on the <b>preview</b> button or press CTRL and ENTER at the same time. If you only want to preview a small part of the code, try highlighting the specific code and then click <b>preview</b> or press CTRL-ENTER");
  }
	
	// give Condition advice
  ////////////////////////
	if(help_type == "Conditions.csv"){	
    if(help_title == ""){
      $("#help_subtitle").html("Blank header");
      $("#help_text").html("Valid <b>conditions</b> settings include: <br><em>" + Object.keys(help_obj.cond).join("<br>")+"</em>");
    } else {
      if(typeof(help_obj.cond[help_title]) !== "undefined"){
        $("#help_subtitle").html(help_obj.cond[help_title].subtitle);
        $("#help_text").html(help_obj.cond[help_title].text);
      } else {
        $("#help_subtitle").html("unknown setting");
        $("#help_text").html("This is not a setting for the <b>conditions</b> sheet that Collector is aware of - maybe it should be in a <b>procedure</b> sheet?" +
        "Valid <b>conditions</b> settings include: <br><em>" + Object.keys(help_obj.cond).join("<br>")+"</em>");
      }
    }
  }
	
  // give Survey advice
  /////////////////////
	if (help_type == "survey"){
		if(help_title == "type"){
			$("#help_subtitle").html("What type of item?");
			var item_type_html = 'You can choose from the following types of survey items: <br>';
			var item_types = Object.keys(help_obj.surv.type);			
			item_types.forEach(function(item_type){
				item_type_html += "<h6>" + item_type + "</h6>";
				item_type_html += help_obj.surv.type[item_type] + "<br><br>";
			});
			item_type_html += "<div id='snippet_help'></div>";			
			$("#help_text").html(item_type_html);
			
			if(Object.keys(megaUberJson.snippets).length > 0){
				var snippet_help_text = "";
				if(typeof(megaUberJson.snippets._help)	 == "undefined"){
					megaUberJson.snippets._help = {};									
				}
				var snippet_names = Object.keys(megaUberJson.snippets).filter(snippet_name => snippet_name !== "_help");
				snippet_names.forEach(function(snippet_name){
					if(typeof(megaUberJson.snippets._help[snippet_name]) == "undefined"){
						var snippet_content = megaUberJson.snippets[snippet_name];
						if(snippet_content.indexOf("/*-----help-----") == -1){
							snippet_help_text += 	"<h6>"+snippet_name+"</h6>"+
																	  "The author of this snippet hasn't (yet) given any advice how to use it. If you are the author, you can add help by writing <br> /*-----help----- <br> then your advice, and then <br>----help-----*/";								
						}
						snippet_help_text += snippet_content.split("-----help-----")[1];
						megaUberJson.snippets._help[snippet_name] = snippet_help_text;
					} else {
						snippet_help_text += megaUberJson.snippets._help[snippet_name];
					}
				});
				$("#snippet_help").html("<br><h5 class='text-primary'>snippets</h5>" + snippet_help_text);				
			}
		} else {
			if(typeof(help_obj.surv[help_title]) !== "undefined"){
				$("#help_subtitle").html(help_obj.surv[help_title].subtitle);
				$("#help_text").html(help_obj.surv[help_title].text);
			} else {
				$("#help_subtitle").html("unknown");
				$("#help_text").html("unknown");
			}
		}
	} 
  
	// give Procedure advice
  ////////////////////////
  
  
  
  
	if(proc_sheets.indexOf(help_type) !== -1){
    if(help_title == "survey"){
      $("#help_subtitle").html("Currently available surveys");
      var def_surveys  = Object.keys(megaUberJson.surveys.default_surveys).sort();
      var user_surveys = Object.keys(megaUberJson.surveys.user_surveys).sort();
      
      def_surveys  = def_surveys.join("<br>");
      user_surveys = user_surveys.join("<br>");
      
      $("#help_text").html("<b>Default surveys</b><br>" + def_surveys + "<br><br><b>User Surveys</b><br>" + user_surveys);
    } else if(help_title == ""){
      $("#help_subtitle").html("Blank header");
      $("#help_text").html("Valid <b>procedure</b> settings include: <br><em>" + Object.keys(help_obj.proc).join("<br>")+"</em>");
    } else {
      if(typeof(help_obj.proc[help_title]) !== "undefined"){
        $("#help_subtitle").html(help_obj.proc[help_title].subtitle);
        $("#help_text").html(help_obj.proc[help_title].text);
      } else {
        $("#help_subtitle").html("unknown setting");
        $("#help_text").html("This is not a setting for the <b>procedure</b> sheet that Collector is aware of - maybe it should be in the <b>conditions</b> sheet?" +
        "Valid <b>conditions</b> settings include: <br><em>" + Object.keys(help_obj.proc).join("<br>")+"</em>");
      }
    }
  }	
  
	// give Stimuli advice
  //////////////////////
	if(stim_sheets.indexOf(help_type) !== -1){
    if(help_title == ""){
      $("#help_subtitle").html("Blank header");      
    } else {    
      $("#help_subtitle").html("{{variable}}");
      $("#help_text").html("You can insert a stimuli into a trial by writing it in this sheet, and then referring to it in a trialtype with {{ variable_name }}. <b>cue</b> and <b>answer</b> are put in as examples, but any variable name can be used. Try to use underscores (_) rather than spaces in variable names though. <br><br> Also, be aware that when referring to a variable within the < script> tag, you should put quotes around it in the script. For example, if you have the variable <b>color</b>, then within the script tag you would write: <br><br> color = '{{color}}';.");
    }
  } else {
    //bootbox.alert("if this isn't the survey sheet, something has gone wrong :-(");      
  }
}

$("#help_btn").on("click",function(){
	if($("#help_area").is(":visible")){
		$("#help_btn").css("font-weight","normal");
		$("#help_btn").removeClass("btn-outline-primary");
		$("#help_btn").removeClass("bg-white");
		$("#help_btn").addClass("btn-primary");
		$("#help_area").hide(500);
	} else {
		$("#help_area").show(500);
		$("#help_btn").css("font-weight","bold");
		$("#help_btn").addClass("btn-outline-primary");
		$("#help_btn").addClass("bg-white");
		$("#help_btn").removeClass("btn-primary");
	}
});

</script>