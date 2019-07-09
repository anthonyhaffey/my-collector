if(isAuthenticated()){
	help_div_content = $(".help_general").html();
	startup_dialog = bootbox.dialog({
		title: 'Welcome!',
		message: '<p id="startup_prog"><i class="fa fa-spin fa-spinner"></i> Loading your uberMegaFile <br><br> Refresh page if this message is here for a while</p>' +
		help_div_content +
		'<button class="btn btn-primary change_tip">Previous</button>' +
		'<button class="btn btn-primary change_tip">Next</button>' +		
		"<button class='btn btn-primary' id='startup_btn' style='display:none'>Start!</button>"
	});
	$(".change_tip").on("click",function(){
		if(this.innerHTML == "Next"){
			help_obj.tip_no++;
		} else {
			help_obj.tip_no--;
		}
		help_obj.tip_no = help_obj.tip_no < 0 ? help_obj.main.length - 1
										: help_obj.tip_no == help_obj.main.length ? 0
										: help_obj.tip_no;
		
		$(".general_tip").hide();
		$(".tip"+help_obj.tip_no).show();
	});
// Create an instance of Dropbox with the access token and use it to
  // fetch and render the files in the users root directory.
  var dbx = new Dropbox({ accessToken: getAccessTokenFromUrl() });
	
	dbx.usersGetCurrentAccount()
	.then(function(account_info){		
		$("#dropbox_account_email").html(account_info.email);
		$("#startup_prog").html("Dropbox account: <a href='https://www.dropbox.com/home/Apps/Open-Collector' target='_blank'>" + account_info.email + "</a> <button class='btn btn-info' id='intro_switch_dbx'>Switch account</button>");
    $("#intro_switch_dbx").on("click",function(){
      dbx.setClientId(CLIENT_ID); // i think is necessary				
      if(local_website.indexOf("localhost") !== -1){
        local_website += "/www";
      }
      authUrl = dbx.getAuthenticationUrl(local_website + '/<?= $_SESSION['version'] ?>');
      authUrl += "&force_reauthentication=true";	
      document.getElementById('authlink').href = authUrl;
      $("#authlink")[0].click();
    });
	})
	.catch(function(error){
		console.dir("Dropbox not logged in yet");
		console.dir(error);	
	});
		
	$_GET = window.location.href.substr(1).split("&").reduce((o,i)=>(u=decodeURIComponent,[k,v]=i.split("="),o[u(k)]=v&&u(v),o),{});
		
	initiate_uberMegaFile(); //detect mega_uber file
			
}	else {
	// Set the login anchors href using dbx.getAuthenticationUrl()
	var dbx = new Dropbox({ clientId: CLIENT_ID });
	var local_website = '<?=$_SESSION['local_website'] ?>';
		
	var authUrl = dbx.getAuthenticationUrl(local_website + '/<?= $_SESSION['version'] ?>');
	document.getElementById('authlink').href = authUrl;
	
	bootbox.confirm("You need to be logged in to dropbox to access your experiments",function(response){
		if(response){
			$("#authlink")[0].click();
		}
	});
}