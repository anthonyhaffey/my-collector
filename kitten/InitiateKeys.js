encrypt_obj = {
  initiate_keys: function (){
    if(typeof(megaUberJson.keys) == "undefined"){
      //var user_keys = nacl.box.keyPair();


      var dialog = bootbox.dialog({
        message: '<p class="text-center mb-0"><i class="fa fa-spin fa-cog"></i> Please wait while the keypair is generated</p>',
          closeButton: false
      });

      var user_keys = rsa.generateKeyPair({bits:2048, e:0X10001});
      // do something in the background
      dialog.modal('hide');

      rsa.generateKeyPair({bits: 2048, workers: 2}, function(err, keypair) {

        var publicKey  = keypair.publicKey;

        // Stringify the object using a replacer function that will explicitly
        // turn functions into strings
        var myObjString = JSON.stringify(publicKey, function(key, val) {
                return (typeof val === 'function') ? '' + val : val;
        });

        // Now, parse back into an object with a reviver function to
        // test for function values and create new functions from them:
        publicKey = JSON.parse(myObjString, function(key, val){

            // Make sure the current value is not null (is a string)
            // and that the first characters are "function"
            if(typeof val === "string" && val.indexOf('function') === 0){

              // Isolate the argument names list
              var start = val.indexOf("(") + 1;
              var end = val.indexOf(")");
              var argListString = val.substring(start,end).split(",");

              // Isolate the body of the function
              var body = val.substr(val.indexOf("{"), val.length - end + 1);

              // Construct a new function using the argument names and body
              // stored in the string:
              return new Function(argListString, body);

            } else {
              // Non-function property, just return the value
              return val;
            }
          }
        );


        /*
            publicKey  = JSON.parse(JSON.stringify(publicKey));
        */
        var privateKey = keypair.privateKey;

        //save public key to dropbox
        /*
        dbx_obj.new_upload({
          path    : "/keys/public.json",
          contents: JSON.stringify(publicKey),
          mode    : "overwrite"
        },function(result){
          dbx.sharingCreateSharedLink({path:boost_path})
            .then(function(result){
              megaUberJson.boosts[response].location = result.url;
              updateUberMegaFile();
            })
            .catch(function(error){
              console.dir("hi 4");
              report_error(error);
            });

        },function(error){
          custom_alert("Error saving boost - please check your console.");
          console.dir(error);
        },
        "filesUpload");
        */
        //save public key to megaUberJson

        //update megaUberJson

        //need symmetric encryption of private key using password here

        encrypt_method = "AES-256-CBC";

        /*
        function encrypt_decrypt($action, $string,$local_key,$this_iv) {
          $output = false;
          $encrypt_method = "AES-256-CBC";
          $secret_key = $local_key;
          $secret_iv = $this_iv;
          // hash
          $key = hash('sha256', $secret_key);

          // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
          $iv = substr(hash('sha256', $secret_iv), 0, 16);
          if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
          } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
          }
          return $output;
        }
        */


        var encrypted = publicKey.encrypt("howdy", 'RSA-OAEP');
        var decrypted = keypair.privateKey.decrypt(encrypted, 'RSA-OAEP');

        bootbox.alert("encrypted = " + encrypted);




        bootbox.alert("decrypted = " + decrypted);
        // keypair.privateKey, keypair.publicKey
      });

      //ask the user for a password to encrypt the private/secret key
    }
  },

  encrypt: function(password) {
    var input = fs.readFileSync('input.txt', {encoding: 'binary'});

    // 3DES key and IV sizes
    var keySize = 24;
    var ivSize = 8;

    // get derived bytes
    // Notes:
    // 1. If using an alternative hash (eg: "-md sha1") pass
    //   "forge.md.sha1.create()" as the final parameter.
    // 2. If using "-nosalt", set salt to null.
    var salt = forge.random.getBytesSync(8);
    // var md = forge.md.sha1.create(); // "-md sha1"
    var derivedBytes = forge.pbe.opensslDeriveBytes(
      password, salt, keySize + ivSize/*, md*/);
    var buffer = forge.util.createBuffer(derivedBytes);
    var key = buffer.getBytes(keySize);
    var iv = buffer.getBytes(ivSize);

    var cipher = forge.cipher.createCipher('3DES-CBC', key);
    cipher.start({iv: iv});
    cipher.update(forge.util.createBuffer(input, 'binary'));
    cipher.finish();

    var output = forge.util.createBuffer();

    // if using a salt, prepend this to the output:
    if(salt !== null) {
      output.putBytes('Salted__'); // (add to match openssl tool output)
      output.putBytes(salt);
    }
    output.putBuffer(cipher.output);

    fs.writeFileSync('input.enc', output.getBytes(), {encoding: 'binary'});
  },

  // openssl enc -d -des3 -in input.enc -out input.dec.txt
  decrypt: function(password) {
    var input = fs.readFileSync('input.enc', {encoding: 'binary'});

    // parse salt from input
    input = forge.util.createBuffer(input, 'binary');
    // skip "Salted__" (if known to be present)
    input.getBytes('Salted__'.length);
    // read 8-byte salt
    var salt = input.getBytes(8);

    // Note: if using "-nosalt", skip above parsing and use
    // var salt = null;

    // 3DES key and IV sizes
    var keySize = 24;
    var ivSize = 8;

    var derivedBytes = forge.pbe.opensslDeriveBytes(
      password, salt, keySize + ivSize);
    var buffer = forge.util.createBuffer(derivedBytes);
    var key = buffer.getBytes(keySize);
    var iv = buffer.getBytes(ivSize);

    var decipher = forge.cipher.createDecipher('3DES-CBC', key);
    decipher.start({iv: iv});
    decipher.update(input);
    var result = decipher.finish(); // check 'result' for true/false

    fs.writeFileSync(
      'input.dec.txt', decipher.output.getBytes(), {encoding: 'binary'});
  }


}
