//
// Eel functions
//////////////////

eel.expose(load_master_json);
function load_master_json(this_json){
  master_json = this_json;
  //renderItems();
  list_surveys();
  first_load = true;
  list_experiments();
  list_boosts();
  list_trialtypes();
	list_graphics();


}

switch(dev_obj.context){
  case "gitpod":
  case "server":
  case "github":
    check_authenticated();   //check dropbox
    break;
  case "localhost":
    eel.load_master_json();  //don't use dropbox
    break;
}
